<?php

namespace BigCommerce\Editor\Gutenberg\Blocks;

use BigCommerce\Shortcodes;

/**
 * A block to display the current user's order history.
 */
class Order_History extends Shortcode_Block {
	/**
	 * The name of the block.
	 *
	 * @var string
	 */
	const NAME = 'bigcommerce/order-history';

	/**
	 * The block's icon.
	 *
	 * @var string
	 */
	protected $icon = 'clipboard';

	/**
	 * The shortcode used for this block.
	 *
	 * @var string
	 */
	protected $shortcode = Shortcodes\Order_History::NAME;

	/**
	 * Returns the block's title.
	 *
	 * @return string The block's title.
	 */
	protected function title() {
		return __( 'BigCommerce Order History', 'bigcommerce' );
	}

	/**
	 * Returns the HTML title for the block.
	 *
	 * @return string The HTML title.
	 */
	protected function html_title() {
		return __( 'Order History', 'bigcommerce' );
	}

	/**
	 * Returns the URL of the image associated with the block.
	 *
	 * @return string The image URL.
	 */
	protected function html_image() {
		return $this->image_url( 'Gutenberg-Block_Order-History.png' );
	}

	/**
	 * Returns an array of keywords related to the block.
	 *
	 * @return array The block's keywords.
	 */
	protected function keywords() {
		$keywords = parent::keywords();
		$keywords[] = __( 'checkout', 'bigcommerce' );
		return $keywords;
	}
}