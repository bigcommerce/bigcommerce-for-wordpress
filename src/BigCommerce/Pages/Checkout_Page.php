<?php


namespace BigCommerce\Pages;

use BigCommerce\Shortcodes;

class Checkout_Page extends Required_Page {
	const NAME = 'bigcommerce_checkout_page_id';

	protected function get_title() {
		return _x( 'Checkout', 'title of the checkout page', 'bigcommerce' );
	}

	protected function get_slug() {
		return _x( 'checkout', 'slug of the checkout page', 'bigcommerce' );
	}

	public function get_content() {
		return sprintf( '[%s]', Shortcodes\Checkout::NAME );
	}

	public function get_post_state_label() {
		return __( 'Checkout Page', 'bigcommerce' );
	}

}