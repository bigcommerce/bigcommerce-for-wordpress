<?php


namespace BigCommerce\Import;


use BigCommerce\Api\v3\ApiException;
use BigCommerce\Api\v3\Model\Category;
use BigCommerce\Taxonomies\Product_Category\Product_Category;

class Product_Category_Mapper extends Term_Mapper {
	protected $taxonomy = Product_Category::NAME;

	/**
	 * @param int $bc_id
	 *
	 * @return Category
	 * @throws ApiException
	 */
	protected function fetch_from_api( $bc_id ) {
		$response = $this->api->getCategoryById( $bc_id );

		return $response->getData();
	}

	protected function get_term_args( \ArrayAccess $bc_term ) {
		return [
			'description' => $this->sanitize_string( $bc_term[ 'description' ] ),
			'parent'      => $this->map( $this->sanitize_int( $bc_term[ 'parent_id' ] ) ),
		];
	}


}