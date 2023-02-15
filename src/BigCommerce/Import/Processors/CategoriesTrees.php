<?php

namespace BigCommerce\Import\Processors;

use BigCommerce\Api\v3\Api\CatalogApi;
use BigCommerce\Api\v3\Model\CategoryCollectionResponse;
use BigCommerce\Taxonomies\Channel\Channel;
use BigCommerce\Taxonomies\Channel\Connections;

trait CategoriesTrees {

	/**
	 * @param \BigCommerce\Api\v3\Api\CatalogApi $api
	 * @param array                              $include_fields
	 *
	 * @return \BigCommerce\Api\v3\Model\CategoryCollectionResponse|array
	 * @throws \BigCommerce\Api\v3\ApiException
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
	 * @param \BigCommerce\Api\v3\Api\CatalogApi $api
	 *
	 * @return array
	 * @throws \BigCommerce\Api\v3\ApiException
	 */
	public function get_trees( CatalogApi $api ): array {
		$connections = new Connections();
		$primary     = $connections->primary();
		$channel_id  = get_term_meta( $primary->term_id, Channel::CHANNEL_ID, true );
		$trees       = $api->getCategoryTree( [ 'channel_id:in' => $channel_id ] );

		return $trees->getData();
	}

}
