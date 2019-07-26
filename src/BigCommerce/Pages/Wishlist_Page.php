<?php


namespace BigCommerce\Pages;

use BigCommerce\Shortcodes;

class Wishlist_Page extends Required_Page {
	const NAME = 'bigcommerce_wishlist_page_id';

	protected function get_title() {
		return _x( 'Wish Lists', 'title of the Wish List page', 'bigcommerce' );
	}

	protected function get_slug() {
		return _x( 'wish-lists', 'slug of the Wish List page', 'bigcommerce' );
	}

	public function get_content() {
		return sprintf( '[%s]', Shortcodes\Wishlist::NAME );
	}

	public function get_post_state_label() {
		return __( 'Wish Lists Page', 'bigcommerce' );
	}

}
