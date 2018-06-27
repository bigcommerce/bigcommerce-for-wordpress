<?php

namespace BigCommerce\Editor\Gutenberg\Blocks;

use BigCommerce\Shortcodes;

/**
 * Class Login_Form
 *
 * A block to display the login form
 */
class Login_Form extends Gutenberg_Block {
	const NAME = 'bigcommerce/login-form';


	public function __construct() {
		parent::__construct();
	}

	public function render( $attributes ) {
		return sprintf( '[%s]', Shortcodes\Login_Form::NAME ); // content will be passed through do_shortcode
	}

	public function js_config() {
		return [
			'name'      => $this->name(),
			'title'     => __( 'BigCommerce Login Form', 'bigcommerce' ),
			'category'  => 'widgets',
			'keywords'  => [
				__( 'users', 'bigcommerce' ),
				__( 'login', 'bigcommerce' ),
				__( 'account', 'bigcommerce' ),
			],
			'shortcode' => sprintf( '[%s]', Shortcodes\Login_Form::NAME ),
		];
	}
}