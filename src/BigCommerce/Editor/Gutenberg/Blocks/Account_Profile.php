<?php

namespace BigCommerce\Editor\Gutenberg\Blocks;

use BigCommerce\Shortcodes;

/**
 * Class Account_Profile
 *
 * A block to display the current user's account profile
 */
class Account_Profile extends Shortcode_Block {
	const NAME = 'bigcommerce/account-profile';

	protected $icon = 'id';
	protected $shortcode = Shortcodes\Account_Profile::NAME;

	protected function title() {
		return __( 'BigCommerce Account Profile', 'bigcommerce' );
	}

	protected function html_title() {
		return __( 'My Account', 'bigcommerce' );
	}

	protected function html_image() {
		return $this->image_url( 'Gutenberg-Block_My-Account.png' );
	}

	protected function keywords() {
		$keywords = parent::keywords();
		$keywords[] = __( 'user', 'bigcommerce' );
		$keywords[] = __( 'account', 'bigcommerce' );
		return $keywords;
	}
}