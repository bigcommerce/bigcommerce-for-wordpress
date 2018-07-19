<?php


namespace BigCommerce\Pages;

use BigCommerce\Shortcodes;


class Check_Balance_Page extends Required_Page {

	const NAME = 'bigcommerce_gift_balance_page_id';

	protected function get_title() {
		return _x( 'Check Balance', 'title of the gift certificate balance page', 'bigcommerce' );
	}

	protected function get_slug() {
		return _x( 'check-balance', 'slug of the gift certificate balance page', 'bigcommerce' );
	}

	public function get_content() {
		return sprintf( '[%s]', Shortcodes\Gift_Certificate_Balance::NAME );
	}

	public function get_post_state_label() {
		return __( 'Gift Certificate Balance Page', 'bigcommerce' );
	}

	protected function get_post_args() {
		$args   = parent::get_post_args();
		$parent = (int) get_option( Gift_Certificate_Page::NAME, 0 );
		if ( $parent ) {
			$args[ 'post_parent' ] = $parent;
		}

		return $args;
	}

}