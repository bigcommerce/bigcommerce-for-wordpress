<?php

namespace BigCommerce\Editor\Gutenberg\Blocks;

use BigCommerce\Shortcodes;

/**
 * A block to display the wishlist.
 */
class Wishlist extends Shortcode_Block {
	/**
	 * The name of the block.
	 *
	 * @var string
	 */
	const NAME = 'bigcommerce/wishlist';

	/**
	 * The icon for the block.
	 *
	 * @var string
	 */
	protected $icon = 'smiley';

	/**
	 * The shortcode associated with the block.
	 *
	 * @var string
	 */
	protected $shortcode = Shortcodes\Wishlist::NAME;

	/**
	 * Returns the title of the block.
	 *
	 * @return string The block's title.
	 */
	protected function title() {
		return __( 'BigCommerce Wish Lists', 'bigcommerce' );
	}

	/**
	 * Returns the HTML title of the block.
	 *
	 * @return string The HTML title for the block.
	 */
	protected function html_title() {
		return __( 'Wish Lists', 'bigcommerce' );
	}

	/**
	 * Returns the URL for the block's image.
	 *
	 * @return string The URL to the image for the block.
	 */
	protected function html_image() {
		return $this->image_url( 'Gutenberg-Block_Wishlist.png' );
	}

	/**
	 * Returns the keywords for the block.
	 *
	 * @return array The block's keywords.
	 */
	protected function keywords() {
		return [
			__( 'users', 'bigcommerce' ),
			__( 'products', 'bigcommerce' ),
			__( 'account', 'bigcommerce' ),
		];
	}
}