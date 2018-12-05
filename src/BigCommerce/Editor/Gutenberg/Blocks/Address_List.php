<?php

namespace BigCommerce\Editor\Gutenberg\Blocks;

use BigCommerce\Shortcodes;

/**
 * Class Address_List
 *
 * A block to display the current user's addresses
 */
class Address_List extends Shortcode_Block {
	const NAME = 'bigcommerce/address-list';

	protected $icon = 'location';
	protected $shortcode = Shortcodes\Address_List::NAME;

	protected function title() {
		return __( 'BigCommerce Address List', 'bigcommerce' );
	}

	protected function html_title() {
		return __( 'My Addresses', 'bigcommerce' );
	}

	protected function html_image() {
		return $this->image_url( 'Gutenberg-Block_Addresses.png' );
	}

	protected function keywords() {
		$keywords = parent::keywords();
		$keywords[] = __( 'checkout', 'bigcommerce' );
		$keywords[] = __( 'shipping', 'bigcommerce' );
		return $keywords;
	}

}