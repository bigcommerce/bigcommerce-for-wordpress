<?php

namespace BigCommerce\Editor\Gutenberg\Blocks;

use BigCommerce\Shortcodes;

/**
 * Class Address_List
 *
 * A block to display the current user's addresses
 */
class Address_List extends Gutenberg_Block {
	const NAME = 'bigcommerce/address-list';


	public function __construct() {
		parent::__construct();
	}

	public function render( $attributes ) {
		return sprintf( '[%s]', Shortcodes\Address_List::NAME ); // content will be passed through do_shortcode
	}

	public function js_config() {
		return [
			'name'      => $this->name(),
			'title'     => __( 'BigCommerce Address List', 'bigcommerce' ),
			'category'  => 'widgets',
			'keywords'  => [
				__( 'checkout', 'bigcommerce' ),
				__( 'shipping', 'bigcommerce' ),
			],
			'shortcode' => sprintf( '[%s]', Shortcodes\Address_List::NAME ),
		];
	}
}