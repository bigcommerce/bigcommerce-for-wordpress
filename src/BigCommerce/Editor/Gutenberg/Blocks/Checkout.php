<?php

namespace BigCommerce\Editor\Gutenberg\Blocks;

use BigCommerce\Shortcodes;

/**
 * Class Cart
 *
 * A block to display the checkout form
 */
class Checkout extends Gutenberg_Block {
	const NAME = 'bigcommerce/checkout';


	public function __construct() {
		parent::__construct();
	}

	public function render( $attributes ) {
		return sprintf( '[%s]', Shortcodes\Checkout::NAME ); // content will be passed through do_shortcode
	}

	public function js_config() {
		return [
			'name'      => $this->name(),
			'title'     => __( 'BigCommerce Checkout', 'bigcommerce' ),
			'category'  => 'widgets',
			'keywords'  => [
				__( 'ecommerce', 'bigcommerce' ),
				__( 'commerce', 'bigcommerce' ),
				__( 'checkout', 'bigcommerce' ),
			],
			'shortcode' => sprintf( '[%s]', Shortcodes\Checkout::NAME ),
			'block_html' => [
				'title' => __( 'Checkout', 'bigcommerce' ),
			],
		];
	}
}