<?php

namespace BigCommerce\Editor\Gutenberg\Blocks;

use BigCommerce\Shortcodes;

/**
 * Class Gift_Certificate_Form
 *
 * A block to display the gift certificate form
 */
class Gift_Certificate_Form extends Gutenberg_Block {
	const NAME = 'bigcommerce/gift-certificate-form';


	public function __construct() {
		parent::__construct();
	}

	public function render( $attributes ) {
		return sprintf( '[%s]', Shortcodes\Gift_Certificate_Form::NAME ); // content will be passed through do_shortcode
	}

	public function js_config() {
		return [
			'name'       => $this->name(),
			'title'      => __( 'BigCommerce Gift Certificates', 'bigcommerce' ),
			'category'   => 'widgets',
			'keywords'   => [
				__( 'checkout', 'bigcommerce' ),
			],
			'shortcode'  => sprintf( '[%s]', Shortcodes\Gift_Certificate_Form::NAME ),
			'block_html' => [
				'title' => __( 'Gift Certificates', 'bigcommerce' ),
			],
		];
	}
}