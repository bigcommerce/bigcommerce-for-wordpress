<?php


namespace BigCommerce\Pages;

use BigCommerce\Shortcodes;

class Account_Page extends Required_Page {
	const NAME = 'bigcommerce_account_page_id';
	const SLUG = 'account-profile';

	protected function get_title() {
		return _x( 'Account Profile', 'title of the account page', 'bigcommerce' );
	}

	protected function get_slug() {
		return _x( self::SLUG, 'slug of the account page', 'bigcommerce' );
	}

	public function get_content() {
		return sprintf( '[%s]', Shortcodes\Account_Profile::NAME );
	}

	public function get_post_state_label() {
		return __( 'Account Profile Page', 'bigcommerce' );
	}

}
