<?php


namespace BigCommerce\Cart;


use BigCommerce\Shortcodes\Cart;

class Cache_Control {
	/**
	 * @param string[] $shortcodes
	 *
	 * @return void
	 * @action template_redirect
	 */
	public function check_for_shortcodes( $shortcodes ) {
		if ( is_singular() ) {
			$object = get_queried_object();
			foreach ( $shortcodes as $shortcode ) {
				if ( strpos( $object->post_content, sprintf( '[%s', $shortcode ) ) ) {
					do_action( 'bigcommerce/do_not_cache' );
					break;
				}
			}
		}
	}

	/**
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