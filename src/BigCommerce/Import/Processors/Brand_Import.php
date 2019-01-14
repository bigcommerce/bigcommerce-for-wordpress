<?php

namespace BigCommerce\Import\Processors;


use BigCommerce\Api\v3\ApiException;
use BigCommerce\Import\Runner\Status;
use BigCommerce\Taxonomies\Brand\Brand;

class Brand_Import extends Term_Import {
	protected function taxonomy() {
		return Brand::NAME;
	}

	protected function running_state() {
		return Status::UPDATING_BRANDS;
	}

	protected function completed_state() {
		return Status::UPDATED_BRANDS;
	}

	/**
	 * @param int $page
	 *
	 * @return \BigCommerce\Api\v3\Model\BrandCollectionResponse
	 * @throws ApiException
	 */
	protected function get_source_data( $page ) {
		$response = $this->catalog_api->getBrands( [
			'page'  => $page,
			'limit' => $this->batch_size,
		] );

		return $response;
	}
}
