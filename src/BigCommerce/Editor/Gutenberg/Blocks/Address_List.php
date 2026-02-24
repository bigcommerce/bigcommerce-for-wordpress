<?php

namespace BigCommerce\Editor\Gutenberg\Blocks;

use BigCommerce\Shortcodes;

/**
 * A block to display the current user's addresses.
 */
class Address_List extends Shortcode_Block {
	/**
	 * The name of the block.
	 * @var string
	 */
	const NAME = 'bigcommerce/address-list';

	/**
	 * The block icon.
	 * @var string
	 */
	protected $icon = 'location';

	/**
	 * The associated shortcode for the address list block.
	 * @var string
	 */
	protected $shortcode = Shortcodes\Address_List::NAME;

	/**
	 * Returns the block title.
	 *
	 * @return string The title of the block.
	 */
	protected function title() {
		return __( 'BigCommerce Address List', 'bigcommerce' );
	}

	/**
	 * Returns the HTML title for the block.
	 *
	 * @return string The HTML title.
	 */
	protected function html_title() {
		return __( 'My Addresses', 'bigcommerce' );
	}

	/**
	 * Returns the URL of the block's image.
	 *
	 * @return string The image URL.
	 */
	protected function html_image() {
		return $this->image_url( 'Gutenberg-Block_Addresses.png' );
	}

	/**
	 * Adds additional keywords for the block.
	 *
	 * @return array The list of keywords, including checkout and shipping.
	 */
	protected function keywords() {
		$keywords = parent::keywords();
		$keywords[] = __( 'checkout', 'bigcommerce' );
		$keywords[] = __( 'shipping', 'bigcommerce' );
		return $keywords;
	}

}