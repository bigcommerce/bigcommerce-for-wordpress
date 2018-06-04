<?php


namespace BigCommerce\Import;


use BigCommerce\Api\v3\ApiException;
use BigCommerce\Taxonomies\Brand\Brand;

class Brand_Mapper extends Term_Mapper {
	protected $taxonomy = Brand::NAME;

	/**
	 * @param int $bc_id
	 *
	 * @return \BigCommerce\Api\v3\Model\Brand
	 * @throws ApiException
	 */
	protected function fetch_from_api( $bc_id ) {
		$response = $this->api->getBrandById( $bc_id );

		return $response->getData();
	}

	protected function get_term_args( \ArrayAccess $bc_term ) {
		return [
			'description' => $this->sanitize_string( $bc_term[ 'meta_description' ] ),
		];
	}


}