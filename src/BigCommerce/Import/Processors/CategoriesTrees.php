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

		$should_include_default_fallback = get_option( Category_Import::CATEGORY_DEFAULT_TREE, false );

		if ( $should_include_default_fallback ) {
			$args['tree_id:in'] = sprintf( '%s,%s', $tree->getId(), 1 );
		}

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
		$result      = $trees->getData();

		if ( ! empty( $result ) ) {
			return $result;
		}

		// Fallback to default store channel
		$trees = $api->getCategoryTree( [ 'channel_id:in' => 1 ] );

		// Set flag that default tree data should be included
		update_option( Category_Import::CATEGORY_DEFAULT_TREE, true );

		return $trees->getData();
	}

}
