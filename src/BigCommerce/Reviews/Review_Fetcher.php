<?php


namespace BigCommerce\Reviews;


use BigCommerce\Api\v3\Api\CatalogApi;
use BigCommerce\Api\v3\ApiException;

class Review_Fetcher {
	/**
	 * @var CatalogApi
	 */
	private $api;

	public function __construct( CatalogApi $api ) {
		$this->api = $api;
	}

	public function fetch( $product_id, $page = 1, $per_page = 12 ) {
		try {
			$response = $this->api->getProductReviews( $product_id, [
				'page'      => $page,
				'limit'     => $per_page,
				'status'    => 1, // 0 = pending, 1 = approved, 2 = disapproved
				'sort'      => 'date_reviewed',
				'direction' => 'desc',
			] );

			return [
				'reviews' => $response->getData(),
				'total'   => $response->getMeta()->getPagination()->getTotal(),
			];
		} catch ( ApiException $e ) {
			return [
				'reviews' => [],
				'total'   => 0,
			];
		}
	}
}
