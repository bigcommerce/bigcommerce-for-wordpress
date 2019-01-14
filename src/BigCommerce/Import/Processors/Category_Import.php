<?php

namespace BigCommerce\Import\Processors;


use BigCommerce\Api\v3\ApiException;
use BigCommerce\Import\Runner\Status;
use BigCommerce\Taxonomies\Product_Category\Product_Category;

class Category_Import extends Term_Import {
	protected function taxonomy() {
		return Product_Category::NAME;
	}

	protected function running_state() {
		return Status::UPDATING_CATEGORIES;
	}

	protected function completed_state() {
		return Status::UPDATED_CATEGORIES;
	}

	/**
	 * @param int $page
	 *
	 * @return \BigCommerce\Api\v3\Model\CategoryCollectionResponse
	 * @throws ApiException
	 */
	protected function get_source_data( $page ) {
		$response = $this->catalog_api->getCategories( [
			'page'  => $page,
			'limit' => $this->batch_size,
		] );

		return $response;
	}
}
