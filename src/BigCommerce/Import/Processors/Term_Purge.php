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

		do_action( 'bigcommerce/log', Error_Log::DEBUG, sprintf( __( 'Removing deleted terms for %s taxonomy', 'bigcommerce' ), $this->taxonomy() ), [
			'page'     => $page,
			'limit'    => $this->batch_size,
			'taxonomy' => $this->taxonomy(),
		] );
		try {
			$local_terms  = $this->get_local_term_ids( $page );
			$remote_terms = $this->get_remote_term_ids( $local_terms );
		} catch ( ApiException $e ) {
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
