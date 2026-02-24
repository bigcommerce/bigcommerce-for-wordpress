<?php

namespace BigCommerce\Import\Processors;


use BigCommerce\Api\v3\ApiException;
use BigCommerce\Import\Runner\Status;
use BigCommerce\Taxonomies\Product_Category\Product_Category;

/**
 * This class handles the process of purging product categories. It extends the 
 * Term_Purge class and uses the CategoriesTrees trait to manage category trees.
 * The purge process involves fetching category data, identifying remote term IDs,
 * and managing the state of the purge process.
 *
 * @package BigCommerce\Import\Processors
 */
class Category_Purge extends Term_Purge {
	use CategoriesTrees;

	/**
	 * Get the taxonomy name for this purge.
	 *
	 * @return string The taxonomy name for product categories.
	 */
	protected function taxonomy() {
		return Product_Category::NAME;
	}

	/**
	 * Get the running state for this purge.
	 *
	 * @return string The running state constant for purging categories.
	 */
	protected function running_state() {
		return Status::PURGING_CATEGORIES;
	}

	/**
	 * Get the completed state for this purge.
	 *
	 * @return string The completed state constant for category purge.
	 */
	protected function completed_state() {
		return Status::PURGED_CATEGORIES;
	}

	/**
	 * Get remote term IDs for the given category IDs.
	 *
	 * This method fetches the term IDs from the remote BigCommerce API, using either 
	 * the MSF categories endpoint or the regular categories endpoint based on the 
	 * store settings. If no IDs are provided or the response is empty, an empty 
	 * array is returned.
	 *
	 * @param array $ids Array of category IDs to fetch remote term IDs for.
	 * 
	 * @return array List of remote term IDs.
	 */
	protected function get_remote_term_ids( array $ids ) {
		if ( empty( $ids ) ) {
			return [];
		}

		$msf_enabled = Store_Settings::is_msf_on();
		if ( $msf_enabled ) {
			$response = $this->get_msf_categories( $this->catalog_api, [
				'category_id:in' => $ids,
				'limit'          => count( $ids ),
				'include_fields' => 'id',
			] );

		} else {
			$response = $this->catalog_api->getCategories( [
				'id:in'          => $ids,
				'limit'          => count( $ids ),
				'include_fields' => 'id',
			] );
		}

		if ( empty( $response ) ) {
			return [];
		}

		return array_map( function ( $object ) use ( $msf_enabled ) {
			return $msf_enabled ? $object['category_id'] : $object['id'];
		}, $response->getData() );
	}
}
