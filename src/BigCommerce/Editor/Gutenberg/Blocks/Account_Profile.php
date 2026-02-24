<?php

namespace BigCommerce\Editor\Gutenberg\Blocks;

use BigCommerce\Shortcodes;

/**
 * A block to display the current user's account profile.
 */
class Account_Profile extends Shortcode_Block {
	/**
	 * The name of the block.
	 * @var string
	 */
	const NAME = 'bigcommerce/account-profile';

	/**
	 * The block icon.
	 * @var string
	 */
	protected $icon = 'id';

	/**
	 * The associated shortcode for the account profile block.
	 * @var string
	 */
	protected $shortcode = Shortcodes\Account_Profile::NAME;

	/**
	 * Returns the block title.
	 *
	 * @return string The title of the block.
	 */
	protected function title() {
		return __( 'BigCommerce Account Profile', 'bigcommerce' );
	}

	/**
	 * Returns the HTML title for the block.
	 *
	 * @return string The HTML title.
	 */
	protected function html_title() {
		return __( 'My Account', 'bigcommerce' );
	}

	/**
	 * Returns the URL of the block's image.
	 *
	 * @return string The image URL.
	 */
	protected function html_image() {
		return $this->image_url( 'Gutenberg-Block_My-Account.png' );
	}

	/**
	 * Adds additional keywords for the block.
	 *
	 * @return array The list of keywords, including user and account.
	 */
	protected function keywords() {
		$keywords = parent::keywords();
		$keywords[] = __( 'user', 'bigcommerce' );
		$keywords[] = __( 'account', 'bigcommerce' );
		return $keywords;
	}
}