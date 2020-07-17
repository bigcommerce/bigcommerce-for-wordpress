<?php


namespace BigCommerce\Shortcodes;

use \BigCommerce\Templates;

class Login_Form implements Shortcode {

	const NAME = 'bigcommerce_signin_form';


	public function __construct() {
	}

	public function render( $attr, $instance ) {
		if ( is_user_logged_in() ) {
			return '';
		}

		$action = filter_input( INPUT_GET, 'action', FILTER_SANITIZE_STRING );

		switch ( $action ) {
			case 'lostpassword':
				$controller = Templates\Lost_Password_Form::factory();
				break;
			default:
				$controller = Templates\Login_Form::factory();
				break;
		}

		return $controller->render();
	}


}