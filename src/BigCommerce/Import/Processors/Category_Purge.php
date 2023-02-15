<?php

namespace BigCommerce\Import\Processors;


use BigCommerce\Api\v3\ApiException;
use BigCommerce\Import\Runner\Status;
use BigCommerce\Taxonomies\Product_Category\Product_Category;

class Category_Purge extends Term_Purge {
	use CategoriesTrees;

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
