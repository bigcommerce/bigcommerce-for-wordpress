<?php


namespace BigCommerce\Pages;

class Shipping_Returns_Page extends Required_Page {
	const NAME = 'bigcommerce_shipping_returns_page_id';

	protected function get_title() {
		return _x( 'Shipping & Returns', 'title of the shipping & returns page', 'bigcommerce' );
	}

	protected function get_slug() {
		return _x( 'shipping-returns', 'slug of the shipping & returns page', 'bigcommerce' );
	}

	public function get_content() {
		$content = [
			__( 'This page is reserved for your Shipping & Returns Policy.', 'bigcommerce' ),
			__( 'For your reference:', 'bigcommerce' ),
			sprintf(
				__( 'Guidelines for writing a Shipping & Returns Policy: %s', 'bigcommerce' ),
				sprintf(
					'<a href="%1$s">%1$s</a>',
					esc_url( 'https://support.bigcommerce.com/articles/Learning/Creating-a-Shipping-and-Returns-Policy' )
				)
			),
			sprintf(
				__( 'Additional resources and examples: %s', 'bigcommerce' ),
				sprintf(
					'<a href="%1$s">%1$s</a>',
					esc_url( 'https://www.bigcommerce.com/blog/create-a-returns-and-exchanges-policy-that-sells/' )
				)
			),
		];
		return implode( "\n\n", $content );
	}

	public function filter_content( $post_id, $content ) {
		return $content;
	}

	public function get_post_state_label() {
		return __( 'Shipping & Returns Page', 'bigcommerce' );
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
