<?php

namespace BigCommerce\Import\Processors;


use BigCommerce\Api\v3\ApiException;
use BigCommerce\Import\Runner\Status;
use BigCommerce\Taxonomies\Brand\Brand;

/**
 * This class handles the process of purging (deleting) BigCommerce brand terms from WordPress.
 * It extends the base Term_Purge class and provides the specific functionality for purging brand terms.
 */
class Brand_Purge extends Term_Purge {

	/**
	 * Get the taxonomy name for the brand purge.
	 *
	 * This method returns the taxonomy name specific to brands in BigCommerce.
	 *
	 * @return string The name of the taxonomy.
	 */
	protected function taxonomy() {
		return Brand::NAME;
	}

	/**
	 * Get the state for the running purge process.
	 *
	 * This method returns the status representing the running state of the brand purge.
	 *
	 * @return string The status indicating the purge process is running.
	 */
	protected function running_state() {
		return Status::PURGING_BRANDS;
	}

	/**
	 * Get the state for the completed purge process.
	 *
	 * This method returns the status representing the completion of the brand purge.
	 *
	 * @return string The status indicating the purge process is completed.
	 */
	protected function completed_state() {
		return Status::PURGED_BRANDS;
	}

	/**
	 * Get the remote term IDs for the given brand IDs.
	 *
	 * This method fetches the brand data from the BigCommerce API using the provided brand IDs and
	 * returns the corresponding term IDs.
	 *
	 * @param array $ids The array of BigCommerce brand IDs to retrieve.
	 *
	 * @return array The array of term IDs corresponding to the provided brand IDs.
	 */
	protected function get_remote_term_ids( array $ids ) {
		if ( empty( $ids ) ) {
			return [];
		}
		$response = $this->catalog_api->getBrands( [
			'id:in'          => $ids,
			'limit'          => count( $ids ),
			'include_fields' => 'id',
		] );

		return array_map( function ( $object ) {
			return $object['id'];
		}, $response->getData() );
	}
}
