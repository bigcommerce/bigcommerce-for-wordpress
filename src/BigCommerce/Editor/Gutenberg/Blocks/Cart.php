<?php

namespace BigCommerce\Editor\Gutenberg\Blocks;

use BigCommerce\Shortcodes;

/**
 * Class Cart
 *
 * A block to display the current user's cart
 */
class Cart extends Gutenberg_Block {
	const NAME = 'bigcommerce/cart';


	public function __construct() {
		parent::__construct();
	}

	public function render( $attributes ) {
		return sprintf( '[%s]', Shortcodes\Cart::NAME ); // content will be passed through do_shortcode
	}

	public function js_config() {
		return [
			'name'      => $this->name(),
			'title'     => __( 'BigCommerce Cart', 'bigcommerce' ),
			'category'  => 'widgets',
			'keywords'  => [
				__( 'ecommerce', 'bigcommerce' ),
				__( 'commerce', 'bigcommerce' ),
				__( 'checkout', 'bigcommerce' ),
			],
			'shortcode' => sprintf( '[%s]', Shortcodes\Cart::NAME ),
			'block_html' => [
				'title' => __( 'Cart', 'bigcommerce' ),
			],
		];
	}
}