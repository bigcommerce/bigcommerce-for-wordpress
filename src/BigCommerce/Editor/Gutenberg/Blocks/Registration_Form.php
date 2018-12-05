<?php

namespace BigCommerce\Editor\Gutenberg\Blocks;

use BigCommerce\Shortcodes;

/**
 * Class Registration_Form
 *
 * A block to display the registration form
 */
class Registration_Form extends Shortcode_Block {
	const NAME = 'bigcommerce/registration-form';


	protected $icon = 'nametag';
	protected $shortcode = Shortcodes\Registration_Form::NAME;

	protected function title() {
		return __( 'BigCommerce Registration Form', 'bigcommerce' );
	}

	protected function html_title() {
		return __( 'Registration Form', 'bigcommerce' );
	}

	protected function html_image() {
		return $this->image_url( 'Gutenberg-Block_Register-Form.png' );
	}

	protected function keywords() {
		return [
			__( 'users', 'bigcommerce' ),
			__( 'registration', 'bigcommerce' ),
			__( 'account', 'bigcommerce' ),
		];
	}
}