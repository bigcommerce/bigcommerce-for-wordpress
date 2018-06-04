<?php


namespace BigCommerce\Shortcodes;

use \BigCommerce\Templates;

class Account_Profile implements Shortcode {

	const NAME = 'bigcommerce_account_profile';


	public function __construct() {
	}

	public function render( $attr, $instance ) {
		$component = new Templates\Profile_Form();
		return $component->render();
	}

}