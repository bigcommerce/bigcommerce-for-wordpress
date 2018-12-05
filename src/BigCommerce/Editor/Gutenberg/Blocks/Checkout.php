<?php

namespace BigCommerce\Editor\Gutenberg\Blocks;

use BigCommerce\Shortcodes;

/**
 * Class Cart
 *
 * A block to display the checkout form
 */
class Checkout extends Shortcode_Block {
	const NAME = 'bigcommerce/checkout';

	protected $icon = 'money';
	protected $shortcode = Shortcodes\Checkout::NAME;

	protected function title() {
		return __( 'BigCommerce Checkout', 'bigcommerce' );
	}

	protected function html_title() {
		return __( 'Checkout', 'bigcommerce' );
	}

	protected function html_image() {
		return $this->image_url( 'Gutenberg-Block_Checkout.png' );
	}

	protected function keywords() {
		$keywords = parent::keywords();
		$keywords[] = __( 'checkout', 'bigcommerce' );
		return $keywords;
	}
}