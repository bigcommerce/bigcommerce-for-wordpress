<?php


namespace BigCommerce\Pages;

use BigCommerce\Shortcodes;

class Checkout_Complete_Page extends Required_Page {
	const NAME = 'bigcommerce_checkout_complete_page_id';

	protected function get_title() {
		return _x( 'Checkout Complete', 'title of the checkout complete page', 'bigcommerce' );
	}

	protected function get_slug() {
		return _x( 'checkout-complete', 'slug of the checkout complete page', 'bigcommerce' );
	}

	public function get_content() {
		$content = [
			__( 'Thanks for your order!', 'bigcommerce' ),
			__( 'We\'ve received your order and will begin processing it right away. For your records, an email confirmation has been sent. It contains details of the items you purchased and also a tracking number (if applicable).', 'bigcommerce' ),
		];
		return implode( "\n\n", $content );
	}

	public function get_post_state_label() {
		return __( 'Checkout Complete Page', 'bigcommerce' );
	}

    public function filter_content( $post_id, $content ) {
		return $content;
	}

	/**
	 * Find an existing post that can be designated as the
	 * required page.
	 *
	 * Since the content of the page is freeform (i.e., not using
	 * a shortcode), it doesn't make sense to match an existing post.
	 *
	 * @return int The ID of the matching post. 0 if none found.
	 */
	protected function match_existing_post() {
		return 0;
	}

	/**
	 * Find all the posts that meet the criteria (e.g., post type,
	 * content) to become this required page.
	 *
	 * Since the content of the page is freeform (i.e., not using
	 * a shortcode), it can match any page.
	 *
	 * @param bool $include_uninstalled Not used in this implementation
	 *
	 * @return int[] Post IDs of potential posts
	 */
	public function get_post_candidates( $include_uninstalled = false ) {
		/** @var \wpdb $wpdb */
		global $wpdb;
		$post_ids     = $wpdb->get_col( $wpdb->prepare(
			"SELECT ID FROM {$wpdb->posts} WHERE post_type=%s AND post_status='publish'",
			$this->get_post_type()
		) );

		$post_ids = array_map( 'intval', $post_ids );

		$post_ids = (array) apply_filters( 'bigcommerce/pages/matching_page_candidates', $post_ids, static::NAME );

		return $post_ids;
	}

}