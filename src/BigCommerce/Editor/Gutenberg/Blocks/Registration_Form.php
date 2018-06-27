<?php

namespace BigCommerce\Editor\Gutenberg\Blocks;

use BigCommerce\Shortcodes;

/**
 * Class Registration_Form
 *
 * A block to display the registration form
 */
class Registration_Form extends Gutenberg_Block {
	const NAME = 'bigcommerce/registration-form';


	public function __construct() {
		parent::__construct();
	}

	public function render( $attributes ) {
		return sprintf( '[%s]', Shortcodes\Registration_Form::NAME ); // content will be passed through do_shortcode
	}

	public function js_config() {
		return [
			'name'      => $this->name(),
			'title'     => __( 'BigCommerce Registration Form', 'bigcommerce' ),
			'category'  => 'widgets',
			'keywords'  => [
				__( 'users', 'bigcommerce' ),
				__( 'registration', 'bigcommerce' ),
				__( 'account', 'bigcommerce' ),
			],
			'shortcode' => sprintf( '[%s]', Shortcodes\Registration_Form::NAME ),
		];
	}
}