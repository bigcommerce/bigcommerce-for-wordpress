<?php


namespace BigCommerce\Shortcodes;

use \BigCommerce\Templates;

class Registration_Form implements Shortcode {

	const NAME = 'bigcommerce_registration_form';


	public function __construct() {
	}

	public function render( $attr, $instance ) {
		if ( ! get_option( 'users_can_register' ) || ! bigcommerce()->credentials_set() || is_user_logged_in() ) {
			return '';
		}
		$component = Templates\Registration_Form::factory();
		return $component->render();
	}

}
