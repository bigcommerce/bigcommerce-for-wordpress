<?php

namespace BigCommerce\Reviews;

use BigCommerce\Post_Types\Product\Product;

/**
 * Class Product_Update_Listener
 *
 * Listens for updates to product meta to determine if the review
 * cache should be flushed
 */
class Product_Update_Listener {
	const TRIGGER_UPDATE = 'bigcommerce/reviews/update';

	/**
	 * Check if updates to product meta include changes to review count or rating sum
	 *
	 * @param int    $meta_id    ID of updated metadata entry.
	 * @param int    $post_id    Post ID.
	 * @param string $meta_key   Meta key.
	 * @param mixed  $meta_value Meta value.
	 *
	 * @action added_post_meta
	 * @action updated_post_meta
	 */
	public function meta_updated( $meta_id, $post_id, $meta_key, $meta_value ) {
		if ( ! in_array( $meta_key, [ Product::REVIEW_COUNT_META_KEY, Product::RATING_SUM_META_KEY ], true ) ) {
			return;
		}
		$product_id = get_post_meta( $post_id, Product::BIGCOMMERCE_ID, true );
		if ( ! wp_next_scheduled( self::TRIGGER_UPDATE, [ $product_id ] ) ) {
			wp_schedule_single_event( time(), self::TRIGGER_UPDATE, [ (int) $product_id ] );
		}
	}
}
