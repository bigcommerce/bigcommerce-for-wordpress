<?php

namespace BigCommerce\Editor\Gutenberg\Blocks;

use BigCommerce\Shortcodes;

/**
 * Class Order_History
 *
 * A block to display the current user's order history
 */
class Order_History extends Gutenberg_Block {
	const NAME = 'bigcommerce/order-history';

	public function __construct() {
		parent::__construct();
	}

	public function render( $attributes ) {
		return sprintf( '[%s]', Shortcodes\Order_History::NAME ); // content will be passed through do_shortcode
	}

	public function js_config() {
		return [
			'name'       => $this->name(),
			'title'      => __( 'BigCommerce Order History', 'bigcommerce' ),
			'category'   => 'widgets',
			'keywords'   => [
				__( 'checkout', 'bigcommerce' ),
			],
			'shortcode'  => sprintf( '[%s]', Shortcodes\Order_History::NAME ),
			'block_html' => [
				'title' => __( 'Order History', 'bigcommerce' ),
			],
		];
	}
}