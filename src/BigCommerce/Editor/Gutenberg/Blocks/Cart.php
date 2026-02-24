<?php

namespace BigCommerce\Editor\Gutenberg\Blocks;

use BigCommerce\Shortcodes;

/**
 * A block to display the current user's cart.
 */
class Cart extends Shortcode_Block {
	/**
	 * The name of the block.
	 * @var string
	 */
	const NAME = 'bigcommerce/cart';

	/**
	 * The block icon.
	 * @var string
	 */
	protected $icon = 'cart';

	/**
	 * The associated shortcode for the cart block.
	 * @var string
	 */
	protected $shortcode = Shortcodes\Cart::NAME;

	/**
	 * Returns the block title.
	 *
	 * @return string The title of the block.
	 */
	protected function title() {
		return __( 'BigCommerce Cart', 'bigcommerce' );
	}

	/**
	 * Returns the HTML title for the block.
	 *
	 * @return string The HTML title.
	 */
	protected function html_title() {
		return __( 'Cart', 'bigcommerce' );
	}

	/**
	 * Returns the URL of the block's image.
	 *
	 * @return string The image URL.
	 */
	protected function html_image() {
		return $this->image_url( 'Gutenberg-Block_Cart.png' );
	}

	/**
	 * Adds additional keywords for the block.
	 *
	 * @return array The list of keywords, including checkout.
	 */
	protected function keywords() {
		$keywords = parent::keywords();
		$keywords[] = __( 'checkout', 'bigcommerce' );
		return $keywords;
	}
}