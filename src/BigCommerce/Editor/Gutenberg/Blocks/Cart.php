<?php

namespace BigCommerce\Editor\Gutenberg\Blocks;

use BigCommerce\Shortcodes;

/**
 * Class Cart
 *
 * A block to display the current user's cart
 */
class Cart extends Shortcode_Block {
	const NAME = 'bigcommerce/cart';

	protected $icon = 'cart';
	protected $shortcode = Shortcodes\Cart::NAME;

	protected function title() {
		return __( 'BigCommerce Cart', 'bigcommerce' );
	}

	protected function html_title() {
		return __( 'Cart', 'bigcommerce' );
	}

	protected function html_image() {
		return $this->image_url( 'Gutenberg-Block_Cart.png' );
	}

	protected function keywords() {
		$keywords = parent::keywords();
		$keywords[] = __( 'checkout', 'bigcommerce' );
		return $keywords;
	}
}