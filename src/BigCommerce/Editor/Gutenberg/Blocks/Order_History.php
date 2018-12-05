<?php

namespace BigCommerce\Editor\Gutenberg\Blocks;

use BigCommerce\Shortcodes;

/**
 * Class Order_History
 *
 * A block to display the current user's order history
 */
class Order_History extends Shortcode_Block {
	const NAME = 'bigcommerce/order-history';

	protected $icon = 'clipboard';
	protected $shortcode = Shortcodes\Order_History::NAME;

	protected function title() {
		return __( 'BigCommerce Order History', 'bigcommerce' );
	}

	protected function html_title() {
		return __( 'Order History', 'bigcommerce' );
	}

	protected function html_image() {
		return $this->image_url( 'Gutenberg-Block_Order-History.png' );
	}

	protected function keywords() {
		$keywords = parent::keywords();
		$keywords[] = __( 'checkout', 'bigcommerce' );
		return $keywords;
	}
}