<?php


namespace BigCommerce\Pages;

use BigCommerce\Shortcodes;

class Cart_Page extends Required_Page {
	const NAME = 'bigcommerce_cart_page_id';

	protected function get_title() {
		return _x( 'Cart', 'title of the cart page', 'bigcommerce' );
	}

	protected function get_slug() {
		return _x( 'cart', 'slug of the cart page', 'bigcommerce' );
	}

	public function get_content() {
		return sprintf( '[%s]', Shortcodes\Cart::NAME );
	}

	public function get_post_state_label() {
		return __( 'Cart Page', 'bigcommerce' );
	}

}