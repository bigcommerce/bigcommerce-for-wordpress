<?php

namespace BigCommerce\Reviews;

use BigCommerce\Api\v3\Model\ProductReview;
use BigCommerce\Post_Types\Product\Product;
use BigCommerce\Post_Types\Product\Product as Product_Post_Type;

/**
 * Class Review_Cache
 *
 * Caches the first page of a product's reviews in post meta
 */
class Review_Cache {
	/**
	 * @var Review_Fetcher
	 */
	private $fetcher;

	public function __construct( Review_Fetcher $fetcher ) {
		$this->fetcher = $fetcher;
	}

	/**
	 * @param int $product_id
	 *
	 * @return void
	 * @action bigcommerce/reviews/update
	 */
	public function update_cache( $product_id ) {
		$post_ids = $this->get_matching_imported_products( $product_id );
		if ( empty( $post_ids ) ) {
			return;
		}

		/**
		 * Filter the number of product reviews to cache
		 *
		 * @param int $count      The max number of reviews that will be cached
		 * @param int $product_id The BigCommerce ID of the product;
		 */
		$count = apply_filters( 'bigcommerce/reviews/cache/per_page', 12, $product_id );
		$data  = $this->fetcher->fetch( $product_id, 1, $count );
		$total = $data['total'];

		$reviews = array_map( function ( ProductReview $review ) use ( $product_id ) {
			$builder = new Review_Builder( $review );

			return $builder->build_review_array( $product_id );
		}, $data['reviews'] );

		foreach ( $post_ids as $p ) {
			update_post_meta( $p, Product::REVIEW_CACHE, $reviews );
			update_post_meta( $p, Product::REVIEWS_APPROVED_META_KEY, $total );
		}
	}


	/**
	 * We only want to import reviews if we have a product
	 * imported into the database corresponding to those
	 * reviews. If the product is disabled for all active
	 * channels, we skip the review import.
	 *
	 * @param int $product_id The BigCommerce product ID
	 *
	 * @return array The post IDs related to the product ID for all channels
	 */
	private function get_matching_imported_products( $product_id ) {
		/** @var \wpdb $wpdb */
		global $wpdb;
		$sql    = "SELECT p.ID FROM {$wpdb->posts} p INNER JOIN {$wpdb->postmeta} m ON p.ID=m.post_id AND m.meta_key=%s WHERE p.post_type=%s AND m.meta_value=%d";
		$result = $wpdb->get_col( $wpdb->prepare( $sql, Product_Post_Type::BIGCOMMERCE_ID, Product_Post_Type::NAME, $product_id ) );

		return array_map( 'intval', $result );
	}
}
