<?php

namespace BigCommerce\Import\Processors;

use BigCommerce\Api\v3\Api\CatalogApi;
use BigCommerce\Api\v3\Model\CategoryCollectionResponse;
use BigCommerce\Taxonomies\Channel\Channel;
use BigCommerce\Taxonomies\Channel\Connections;

/**
 * This trait provides methods for working with BigCommerce category trees.
 * It includes functionality to fetch categories within a specific category tree.
 */
trait CategoriesTrees {

	/**
	 * Get the categories in the MSF category tree.
	 *
	 * This method retrieves categories within the MSF category tree by fetching the tree ID,
	 * then requesting a batch of categories from the BigCommerce API.
	 *
	 * @param \BigCommerce\Api\v3\Api\CatalogApi $api The BigCommerce Catalog API instance.
	 * @param array                              $params Additional parameters for the category query (optional).
	 *
	 * @return \BigCommerce\Api\v3\Model\CategoryCollectionResponse|array A collection of categories or an empty array.
	 * @throws \BigCommerce\Api\v3\ApiException Throws an exception if the API request fails.
	 */
	public function get_msf_categories( CatalogApi $api, array $params = [] ) {
		$trees = $this->get_trees( $api );

		if ( empty( $trees ) ) {
			return [];
		}

		$tree = array_shift( $trees );

		$args = [
			'tree_id:in' => $tree->getId(),
		];

		if ( ! empty( $params ) ) {
			$args = array_merge( $args, $params );
		}

		return $api->getCategoriesBatch( $args );
	}

	/**
	 * Get all category trees associated with a given channel.
	 *
	 * This method fetches category trees from BigCommerce by retrieving the channel ID
	 * associated with the primary connection and querying the BigCommerce API for
	 * available category trees.
	 *
	 * @param \BigCommerce\Api\v3\Api\CatalogApi $api The BigCommerce Catalog API instance.
	 *
	 * @return array An array of category trees.
	 * @throws \BigCommerce\Api\v3\ApiException Throws an exception if the API request fails.
	 */
	public function get_trees( CatalogApi $api ): array {
		$connections = new Connections();
		$primary     = $connections->primary();
		$channel_id  = get_term_meta( $primary->term_id, Channel::CHANNEL_ID, true );
		$trees       = $api->getCategoryTree( [ 'channel_id:in' => $channel_id ] );

		return $trees->getData();
	}

}
