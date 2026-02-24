<?php

namespace BigCommerce\Import\Processors;


use BigCommerce\Api\v3\Api\CatalogApi;
use BigCommerce\Api\v3\ApiException;
use BigCommerce\Import\No_Cache_Options;
use BigCommerce\Import\Runner\Status;
use BigCommerce\Logging\Error_Log;

/**
 * Class Term_Purge
 *
 * Deletes imported terms that no longer exist in BigCommerce
 */
abstract class Term_Purge implements Import_Processor {
	use No_Cache_Options;

	/**
	 * Option name for storing the term purge state in WordPress options table.
	 * 
	 * @var string STATE_OPTION
	 */
	const STATE_OPTION = 'bigcommerce_purge_terms_state';

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
	 * Gets the WordPress taxonomy identifier that this purge processor handles.
	 * @return string The taxonomy name (e.g., 'product_category', 'product_brand').
	 */
	abstract protected function taxonomy();

	/**
	 * Gets the status identifier for when this term purge process is running.
	 * @return string The status identifier for the running state.
	 */
	abstract protected function running_state();

	/**
	 * Gets the status identifier for when this term purge process is completed.
	 * @return string The status identifier for the completed state.
	 */
	abstract protected function completed_state();

	/**
	 * Executes the term purge process.
	 * 
	 * This method handles the deletion of WordPress terms that no longer exist in BigCommerce.
	 * It processes terms in batches, comparing local terms with remote BigCommerce data.
	 * The process tracks its state to support pagination and can be resumed if interrupted.
	 * 
	 * @return void
	 * 
	 * @throws \BigCommerce\Api\v3\ApiException If there's an error communicating with the BigCommerce API.
	 */
	public function run() {
		$status = new Status();
		$status->set_status( $this->running_state() );

		$page = $this->get_page();
		if ( empty( $page ) ) {
			$page = 1;
		}

		do_action( 'bigcommerce/log', Error_Log::DEBUG, sprintf( __( 'Removing deleted terms for %s taxonomy', 'bigcommerce' ), $this->taxonomy() ), [
			'page'     => $page,
			'limit'    => $this->batch_size,
			'taxonomy' => $this->taxonomy(),
		] );
		try {
			$local_terms  = $this->get_local_term_ids( $page );
			$remote_terms = $this->get_remote_term_ids( $local_terms );
		} catch ( ApiException $e ) {
			/**
			 * Action to log import errors during the BigCommerce import process.
			 *
			 * This action is triggered when there is an error during the import process. It logs the error message and context for further analysis.
			 *
			 * @param string $message The error message to be logged.
			 * @param array $context Additional context for the error.
			 */
			do_action( 'bigcommerce/import/error', $e->getMessage(), [
				'response' => $e->getResponseBody(),
				'headers'  => $e->getResponseHeaders(),
			] );
			do_action( 'bigcommerce/log', Error_Log::DEBUG, $e->getTraceAsString(), [] );

			return;
		}

		$deleted_terms = array_diff( $local_terms, $remote_terms );

		// Create/update each term
		foreach ( $deleted_terms as $term_id => $bigcommerce_id ) {
			do_action( 'bigcommerce/log', Error_Log::DEBUG, sprintf( __( 'Deleting term %s from taxonomy %s', 'bigcommerce' ), $term_id, $this->taxonomy() ), [
				'bigcommerce_id'     => $bigcommerce_id,
			] );
			wp_delete_term( $term_id, $this->taxonomy() );
		}

		if ( count( $local_terms ) < $this->batch_size ) {
			$status->set_status( $this->completed_state() );
			$this->clear_state();
		} else {
			do_action( 'bigcommerce/log', Error_Log::DEBUG, sprintf( __( '%s purge ready for next page of terms', 'bigcommerce' ), $this->taxonomy() ), [
				'next'     => $page + 1,
				'taxonomy' => $this->taxonomy(),
			] );
			$this->set_page( $page + 1 );
		}
	}

	/**
	 * Get the IDs of all previously imported terms
	 *
	 * @param int $page
	 *
	 * @return array
	 */
	private function get_local_term_ids( $page ) {
		/** @var \wpdb $wpdb */
		global $wpdb;
		$offset = ( $page - 1 ) * $this->batch_size;
		$sql    = "SELECT m.term_id, m.meta_value
		        FROM {$wpdb->termmeta} m
		        INNER JOIN {$wpdb->term_taxonomy} tt ON m.term_id=tt.term_id
		        WHERE m.meta_key=%s AND tt.taxonomy=%s
		        ORDER BY m.term_id
		        LIMIT %d, %d";
		$sql    = $wpdb->prepare( $sql, 'bigcommerce_id', $this->taxonomy(), $offset, $this->batch_size );

		return wp_list_pluck( $wpdb->get_results( $sql ), 'meta_value', 'term_id' );
	}


	/**
	 * Get the IDs of all terms found in the API that match
	 * the known terms
	 *
	 * @param int[] $ids The IDs of terms to check against
	 *
	 * @return int[]
	 *
	 * @throws ApiException
	 */
	abstract protected function get_remote_term_ids( array $ids );

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
