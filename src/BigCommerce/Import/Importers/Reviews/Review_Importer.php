<?php


namespace BigCommerce\Import\Importers\Reviews;

use BigCommerce\Api\v3\Api\CatalogApi;
use BigCommerce\Api\v3\Model\Product;
use BigCommerce\Api\v3\Model\ProductReview;
use BigCommerce\Post_Types\Product\Product as Product_Post_Type;

class Review_Importer {
	/**
	 * @var Product The Product from the BigCommerce catalog API
	 */
	private $product;

	/**
	 * @var CatalogApi
	 */
	private $catalog;

	/**
	 * Product_Importer constructor.
	 *
	 * @param Product    $product
	 * @param CatalogApi $catalog
	 */
	public function __construct( Product $product, CatalogApi $catalog ) {
		$this->product = $product;
		$this->catalog = $catalog;
	}

	/**
	 * @return void
	 */
	public function import() {
		/** @var \wpdb $wpdb */
		global $wpdb;

		if ( $this->product->getReviewsCount() == 0 || $this->has_matching_imported_product( $this->product['id'] ) ) {
			$reviews = [];
		} else {
			$fetch   = new Review_Fetcher( $this->catalog, $this->product['id'] );
			$reviews = array_map( function ( ProductReview $review ) {
				$builder = new Review_Builder( $review );

				return $builder->build_review_array( $this->product['id'] );
			}, $fetch->fetch() );
		}

		$existing_reviews = array_map( 'intval', $wpdb->get_col( $wpdb->prepare( "SELECT review_id FROM {$wpdb->bc_reviews} WHERE bc_id=%d", $this->product['id'] ) ) );
		$valid_review_ids = array_map( 'intval', wp_list_pluck( $reviews, 'review_id' ) );

		$to_remove = array_diff( $existing_reviews, $valid_review_ids );

		foreach ( $to_remove as $review_id ) {
			$wpdb->delete( $wpdb->bc_reviews, [ 'review_id' => $review_id ], [ '%d' ] );
		}

		foreach ( $reviews as $review ) {
			if ( in_array( $review['review_id'], $existing_reviews ) ) {
				$wpdb->update( $wpdb->bc_reviews, $review, [ 'review_id' => $review['review_id'] ] );
			} else {
				$wpdb->insert( $wpdb->bc_reviews, $review );
			}
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
	 * @return bool Whether there are products in the database with the given product ID
	 */
	private function has_matching_imported_product( $product_id ) {
		/** @var \wpdb $wpdb */
		global $wpdb;
		$sql = "SELECT p.ID FROM {$wpdb->posts} p INNER JOIN {$wpdb->postmeta} m ON p.ID=m.post_id AND m.meta_key=%s WHERE p.post_type=%s AND m.meta_value=%d";
		$result = $wpdb->get_col( $wpdb->prepare( $sql, Product_Post_Type::BIGCOMMERCE_ID, Product_Post_Type::NAME, $product_id ) );
		return count($result) === 0;
	}
}