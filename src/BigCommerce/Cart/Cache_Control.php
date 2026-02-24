<?php


namespace BigCommerce\Cart;


use BigCommerce\Shortcodes\Cart;

/**
 * Class Cache_Control
 *
 * Handles caching logic based on the presence of specific shortcodes in the content of a singular page.
 * If certain shortcodes are found, it disables caching for that page.
 */
class Cache_Control {
	/**
	 * Checks for specific shortcodes in the content of the queried object.
	 * If any shortcode is found, it triggers the 'bigcommerce/do_not_cache' action to prevent caching of the page.
	 *
	 * @param string[] $shortcodes An array of shortcodes to check for in the post content.
	 *
	 * @return void
	 * @action template_redirect
	 */
	public function check_for_shortcodes( $shortcodes ) {
		if ( is_singular() ) {
			$object = get_queried_object();
			foreach ( $shortcodes as $shortcode ) {
				if ( strpos( $object->post_content, sprintf( '[%s', $shortcode ) ) ) {
					/**
					 * Fires when a BigCommerce shortcode is detected in the post content.
					 * This action triggers cache prevention mechanisms to ensure dynamic cart content is always fresh.
					 */
					do_action( 'bigcommerce/do_not_cache' );
					break;
				}
			}
		}
	}

	/**
	 * Disables caching for the page by sending the appropriate no-cache headers
	 * and defining constants to prevent caching mechanisms from caching the page.
	 *
	 * @return void
	 * @action bigcommerce/do_not_cache
	 */
	public function do_not_cache() {
		nocache_headers();
		if ( !defined('DONOTCACHEPAGE') ) {
			define('DONOTCACHEPAGE', TRUE);
		}
		if ( function_exists('batcache_cancel') ) {
			batcache_cancel();
		}
	}

	private function cart_shortcode() {
		return sprintf( '[%s', Cart::NAME );
	}
}