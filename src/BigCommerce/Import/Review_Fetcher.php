<?php


namespace BigCommerce\Import;


use BigCommerce\Api\v3\ApiException;
use BigCommerce\Api\v3\Api\CatalogApi;
use BigCommerce\Api\v3\Model\ProductReview;

class Review_Fetcher {
	/**
	 * @var CatalogApi
	 */
	private $api;
	/**
	 * @var int The BigCommerce ID of the product
	 */
	private $product_id;

	private $per_page = 100;

	public function __construct( CatalogApi $api, $product_id ) {
		$this->api        = $api;
		$this->product_id = $product_id;
		/**
		 * Filter the limit for reviews in each request to the API. A smaller number
		 * will result in more requests for products with large numbers of reviews.
		 * The maximum is 250.
		 *
		 * @param int $per_page The maximum number of reviews in each API response
		 */
		$this->per_page = min( absint( apply_filters( 'bigcommerce/import/reviews/per_page', $this->per_page ) ), 250 );
	}

	public function fetch() {
		$reviews = [];
		$page    = 0;
		try {
			do {
				$page ++;
				$response  = $this->api->getProductReviews( $this->product_id, null, null, $page, $this->per_page );
				$reviews   = array_merge( $reviews, $response->getData() );
				$max_pages = $response->getMeta()->getPagination()->getTotalPages();
			} while ( $page < $max_pages );
		} catch ( ApiException $e ) {
			return $reviews; // just return what we have
		}

		return $reviews;
	}


}