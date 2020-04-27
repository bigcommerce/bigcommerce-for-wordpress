<?php

namespace BigCommerce\Import\Processors;


use BigCommerce\Api\v3\Api\CatalogApi;
use BigCommerce\Api\v3\ApiException;
use BigCommerce\Import\Importers\Terms\Term_Strategy_Factory;
use BigCommerce\Import\No_Cache_Options;
use BigCommerce\Import\Runner\Status;
use BigCommerce\Logging\Error_Log;

abstract class Term_Import implements Import_Processor {
	use No_Cache_Options;

	const STATE_OPTION = 'bigcommerce_import_terms_state';

	/**
	 * @var CatalogApi
	 */
	public $catalog_api;

	/**
	 * @var int
	 */
	public $batch_size;

	/**
	 * Category_Import constructor.
	 *
	 * @param CatalogApi $catalog_api
	 * @param int        $batch_size
	 */
	public function __construct( CatalogApi $catalog_api, $batch_size ) {
		$this->catalog_api = $catalog_api;
		$this->batch_size  = $batch_size;
	}

	/**
	 * @return string The name of the taxonomy to update
	 */
	abstract protected function taxonomy();

	/**
	 * @return string The name of the state to set while the import is running
	 */
	abstract protected function running_state();

	/**
	 * @return string The name of the state to set when the import is complete
	 */
	abstract protected function completed_state();

	public function run() {
		$status = new Status();
		$status->set_status( $this->running_state() );

		$page = $this->get_page();
		if ( empty( $page ) ) {
			$page = 1;
		}

		do_action( 'bigcommerce/log', Error_Log::DEBUG, sprintf( __( 'Importing terms for %s taxonomy', 'bigcommerce' ), $this->taxonomy() ), [
			'page'     => $page,
			'limit'    => $this->batch_size,
			'taxonomy' => $this->taxonomy(),
		] );
		try {
			$response = $this->get_source_data( $page );
			$terms    = $response->getData();
		} catch ( ApiException $e ) {
			do_action( 'bigcommerce/import/error', $e->getMessage(), [
				'response' => $e->getResponseBody(),
				'headers'  => $e->getResponseHeaders(),
			] );
			do_action( 'bigcommerce/log', Error_Log::DEBUG, $e->getTraceAsString(), [] );

			return;
		}

		// Allow more HTML in term descriptions than WP default
		$terms_descriptions_filtered = has_filter( 'pre_term_description', 'wp_filter_kses' );
		if ( $terms_descriptions_filtered ) {
			remove_filter( 'pre_term_description', 'wp_filter_kses' );
		}

		// Create/update each term
		foreach ( $terms as $term ) {
			$strategy_factory = new Term_Strategy_Factory( $term, $this->taxonomy() );
			$strategy         = $strategy_factory->get_strategy();
			$strategy->do_import();
		}

		// Put the term description filter back where we found it
		if ( $terms_descriptions_filtered ) {
			add_filter( 'pre_term_description', 'wp_filter_kses' );
		}


		$total_pages = $response->getMeta()->getPagination()->getTotalPages();
		if ( $total_pages > $page ) {
			do_action( 'bigcommerce/log', Error_Log::DEBUG, sprintf( __( '%s import ready for next page of terms', 'bigcommerce' ), $this->taxonomy() ), [
				'next'     => $page + 1,
				'taxonomy' => $this->taxonomy(),
			] );
			$this->set_page( $page + 1 );
		} else {
			$status->set_status( $this->completed_state() );
			$this->clear_state();
		}
	}


	/**
	 * @param int $page
	 *
	 * @return \BigCommerce\Api\v3\Model\CategoryCollectionResponse|\BigCommerce\Api\v3\Model\BrandCollectionResponse The
	 *                                                                                                                API
	 *                                                                                                                response
	 *                                                                                                                object
	 * @throws ApiException
	 */
	abstract protected function get_source_data( $page );

	private function get_page() {
		$state = $this->get_state();
		if ( ! array_key_exists( $this->taxonomy(), $state ) ) {
			return 0;
		}

		return $state[ $this->taxonomy() ];
	}

	private function set_page( $page ) {
		$state                      = $this->get_state();
		$state[ $this->taxonomy() ] = (int) $page;
		$this->set_state( $state );
	}

	private function get_state() {
		$state = $this->get_option( self::STATE_OPTION, [] );
		if ( ! is_array( $state ) ) {
			return [];
		}

		return $state;
	}

	private function set_state( array $state ) {
		$this->update_option( self::STATE_OPTION, $state, false );
	}

	private function clear_state() {
		$this->delete_option( self::STATE_OPTION );
	}
}
