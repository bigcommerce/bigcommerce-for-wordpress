<?php

namespace BigCommerce\Editor\Gutenberg\Blocks;

use BigCommerce\Shortcodes;

/**
 * Class Login_Form
 *
 * A block to display the login form
 */
class Login_Form extends Shortcode_Block {
	const NAME = 'bigcommerce/login-form';

	protected $icon = 'admin-users';
	protected $shortcode = Shortcodes\Login_Form::NAME;

	protected function title() {
		return __( 'BigCommerce Login Form', 'bigcommerce' );
	}

	protected function html_title() {
		return __( 'Login Form', 'bigcommerce' );
	}

	protected function html_image() {
		return $this->image_url( 'Gutenberg-Block_LogIn.png' );
	}

	protected function keywords() {
		return [
			__( 'users', 'bigcommerce' ),
			__( 'login', 'bigcommerce' ),
			__( 'account', 'bigcommerce' ),
		];
	}
}