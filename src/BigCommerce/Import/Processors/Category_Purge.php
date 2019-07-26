<?php

namespace BigCommerce\Import\Processors;


use BigCommerce\Api\v3\ApiException;
use BigCommerce\Import\Runner\Status;
use BigCommerce\Taxonomies\Product_Category\Product_Category;

class Category_Purge extends Term_Purge {
	protected function taxonomy() {
		return Product_Category::NAME;
	}

	protected function running_state() {
		return Status::PURGING_CATEGORIES;
	}

	protected function completed_state() {
		return Status::PURGED_CATEGORIES;
	}

	protected function get_remote_term_ids( array $ids ) {
		if ( empty( $ids ) ) {
			return [];
		}
		$response = $this->catalog_api->getCategories( [
			'id:in'          => $ids,
			'limit'          => count( $ids ),
			'include_fields' => 'id',
		] );

		return array_map( function ( $object ) {
			return $object['id'];
		}, $response->getData() );
	}
}
