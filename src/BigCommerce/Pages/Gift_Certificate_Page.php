<?php


namespace BigCommerce\Pages;

use BigCommerce\Shortcodes;


class Gift_Certificate_Page extends Required_Page {

	const NAME = 'bigcommerce_gift_certificate_page_id';

	protected function get_title() {
		return _x( 'Purchase Gift Certificate', 'title of the gift certificate page', 'bigcommerce' );
	}

	protected function get_slug() {
		return _x( 'gift-certificate', 'slug of the gift certificate page', 'bigcommerce' );
	}

	public function get_content() {
		return sprintf( '[%s]', Shortcodes\Gift_Certificate_Form::NAME );
	}

	public function get_post_state_label() {
		return __( 'Gift Certificate Page', 'bigcommerce' );
	}

}