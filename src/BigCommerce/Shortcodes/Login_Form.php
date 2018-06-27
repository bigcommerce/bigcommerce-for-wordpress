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

		$action = isset( $_GET[ 'action' ] ) ? $_GET[ 'action' ] : '';

		switch ( $action ) {
			case 'lostpassword':
				$controller = new Templates\Lost_Password_Form();
				break;
			default:
				$controller = new Templates\Login_Form();
				break;
		}

		return $controller->render();
	}


}