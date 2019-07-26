<?php

namespace BigCommerce\Editor\Gutenberg\Blocks;

use BigCommerce\Shortcodes;

/**
 * Class Registration_Form
 *
 * A block to display the registration form
 */
class Wishlist extends Shortcode_Block {
	const NAME = 'bigcommerce/wishlist';

	protected $icon = 'smiley';
	protected $shortcode = Shortcodes\Wishlist::NAME;

	protected function title() {
		return __( 'BigCommerce Wish Lists', 'bigcommerce' );
	}

	protected function html_title() {
		return __( 'Wish Lists', 'bigcommerce' );
	}

	protected function html_image() {
		return $this->image_url( 'Gutenberg-Block_Wishlist.png' );
	}

	protected function keywords() {
		return [
			__( 'users', 'bigcommerce' ),
			__( 'products', 'bigcommerce' ),
			__( 'account', 'bigcommerce' ),
		];
	}
}