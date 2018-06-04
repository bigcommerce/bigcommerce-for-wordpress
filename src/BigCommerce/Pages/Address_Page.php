<?php


namespace BigCommerce\Pages;

use BigCommerce\Shortcodes;

class Address_Page extends Required_Page {
	const NAME = 'bigcommerce_address_page_id';

	protected function get_title() {
		return _x( 'Addresses', 'title of the addresses page', 'bigcommerce' );
	}

	protected function get_slug() {
		return _x( 'addresses', 'slug of the addresses page', 'bigcommerce' );
	}

	public function get_content() {
		return sprintf( '[%s]', Shortcodes\Address_List::NAME );
	}

	public function get_post_state_label() {
		return __( 'Addresses Page', 'bigcommerce' );
	}

}