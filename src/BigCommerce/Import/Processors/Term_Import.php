<?php

namespace BigCommerce\Import\Processors;


use BigCommerce\Api\v3\Api\CatalogApi;
use BigCommerce\Api\v3\ApiException;
use BigCommerce\GraphQL\GraphQL_Processor;
use BigCommerce\Import\Importers\Terms\Term_Strategy_Factory;
use BigCommerce\Import\No_Cache_Options;
use BigCommerce\Import\Runner\Status;
use BigCommerce\Logging\Error_Log;
use BigCommerce\Taxonomies\Brand\Brand;
use BigCommerce\Taxonomies\Product_Category\Product_Category;

abstract class Term_Import implements Import_Processor {
	use No_Cache_Options;

	const STATE_OPTION      = 'bigcommerce_import_terms_state';
	const BRANDS_CHECKPOINT = 'bigcommerce_import_brands_checkpoint';

	/**
	 * @var CatalogApi
	 */
	public $catalog_api;

	/**
	 * @var GraphQL_Processor
	 */
	public $gql_processor;

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
	public function __construct( CatalogApi $catalog_api, GraphQL_Processor $gql_processor, $batch_size ) {
		$this->catalog_api = $catalog_api;
		$this->gql_processor = $gql_processor;
		$this->batch_size  = $batch_size;
	}

	/**
	 * @return string The name of the taxonomy to update
	 */
	abstract protected function taxonomy();

	abstract protected function get_fallback_terms();

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


		do_action( 'bigcommerce/log', Error_Log::DEBUG, sprintf( __( 'Importing terms for %s taxonomy', 'bigcommerce' ), $this->taxonomy() ), [
			'limit'    => $this->batch_size,
			'taxonomy' => $this->taxonomy(),
		] );

		$rest_fallback = false;

		try {
			$terms = $this->get_source_data();
			// Fallback to old categories pull
			if ( empty( $terms ) && $this->taxonomy() === Product_Category::NAME ) {
				$terms         = $this->get_fallback_terms();
				$rest_fallback = true;
			}
		} catch ( \Throwable $e ) {
			do_action( 'bigcommerce/log', Error_Log::DEBUG, $e->getMessage(), [
				'response' => method_exists( $e, 'getResponseBody' ) ? $e->getResponseBody() : $e->getTraceAsString(),
				'headers'  => method_exists( $e, 'getResponseHeaders' ) ? $e->getResponseHeaders() : '',
			] );

			$terms         = $this->get_fallback_terms();
			$rest_fallback = true;
		}

		if ( empty( $terms ) ) {
			do_action( 'bigcommerce/log', Error_Log::DEBUG, sprintf( __( 'Could not find terms for %s. Wrapping up step and go to the next one', 'bigcommerce' ), $this->taxonomy() ), [] );
			$status->set_status( $this->completed_state() );
			$this->clear_state();

			return;
		}

		// Allow more HTML in term descriptions than WP default
		$terms_descriptions_filtered = has_filter( 'pre_term_description', 'wp_filter_kses' );
		if ( $terms_descriptions_filtered ) {
			remove_filter( 'pre_term_description', 'wp_filter_kses' );
		}

		// Create/update each term
		foreach ( $terms as $term ) {
			$this->do_term_import( $term, $rest_fallback );
		}

		// Put the term description filter back where we found it
		if ( $terms_descriptions_filtered ) {
			add_filter( 'pre_term_description', 'wp_filter_kses' );
		}

		$status->set_status( $this->completed_state() );
		$this->clear_state();
	}

	protected function do_term_import( $term, $fallback = false ) {
		if ( ! $fallback ) {
			$parsed = $this->parse_gql_term( $term );
			array_walk( $parsed, function ( $single ) {
				$strategy_factory = new Term_Strategy_Factory( $single, $this->taxonomy() );
				$strategy         = $strategy_factory->get_strategy();
				$strategy->do_import();
			} );

			return;
		}

		$strategy_factory = new Term_Strategy_Factory( $term, $this->taxonomy() );
		$strategy         = $strategy_factory->get_strategy();
		$strategy->do_import();
	}


	/**
	 * @param string $cursor
	 *
	 * @return array The API response object
	 * @throws ApiException
	 */
	abstract public function get_source_data( $cursor = '' );

	/**
	 * @param \StdClass $term
	 *
	 * @return array
	 */
	abstract protected function parse_gql_term( $term = null ): array;

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

	/**
	 * Parse GraphQL response. If Brands have more than 1 page return cursor to continue
	 *
	 * @param string $raw_response
	 *
	 * @return array|mixed
	 */
	protected function handle_graph_ql_response( $raw_response = '' ) {
		if ( empty( $raw_response ) || empty( $raw_response->data->site ) ) {
			return [];
		}

		switch ( $this->taxonomy() ) {
			case Brand::NAME:
				if ( $raw_response->data->site->brands->pageInfo->hasNextPage ) {
					// Store brands data and return next cursor in order to retrieve all brands
					$checkpoint = $this->get_option( self::BRANDS_CHECKPOINT, [] );
					$this->update_option( self::BRANDS_CHECKPOINT, array_merge( $checkpoint, $raw_response->data->site->brands->edges) );

					return $raw_response->data->site->brands->pageInfo->endCursor;
				}

				return $raw_response->data->site->brands->edges;
			default:
				return $raw_response->data->site->categoryTree;
		}
	}

	protected function get_page() {
		$state = $this->get_state();
		if ( ! array_key_exists( $this->taxonomy(), $state ) ) {
			return 0;
		}

		return $state[ $this->taxonomy() ];
	}

	protected function set_page( $page ) {
		$state                      = $this->get_state();
		$state[ $this->taxonomy() ] = (int) $page;
		$this->set_state( $state );
	}

}
