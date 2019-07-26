<?php

namespace BigCommerce\Import\Processors;


use BigCommerce\Api\v3\ApiException;
use BigCommerce\Import\Runner\Status;
use BigCommerce\Taxonomies\Brand\Brand;

class Brand_Purge extends Term_Purge {
	protected function taxonomy() {
		return Brand::NAME;
	}

	protected function running_state() {
		return Status::PURGING_BRANDS;
	}

	protected function completed_state() {
		return Status::PURGED_BRANDS;
	}

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
