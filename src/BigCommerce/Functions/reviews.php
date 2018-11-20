<?php

namespace BigCommerce\Functions;


use BigCommerce\Shortcodes\Product_Reviews as Shortcode_Product_Reviews;

/**
 * Render reviews section for a product by BigCommerce ID
 *
 * @param int $product_id The BigCommerce ID of the product
 *
 * @return string The rendered product reviews
 */
function product_reviews( $product_id ) {
	return do_shortcode( sprintf( '[%s id="%d"]', Shortcode_Product_Reviews::NAME, $product_id ) );
}

/**
 * Render reviews section for a product by post ID
 *
 * @param int $post_id The WordPress ID of the product post
 *
 * @return string The rendered product reviews
 */
function product_post_reviews( $post_id ) {
	return do_shortcode( sprintf( '[%s post_id="%d"]', Shortcode_Product_Reviews::NAME, $post_id ) );
}