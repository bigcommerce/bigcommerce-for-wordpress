<?php


namespace BigCommerce\Shortcodes;

use \BigCommerce\Templates;

class Address_List implements Shortcode {

	const NAME = 'bigcommerce_shipping_address_list';


	public function __construct() {
	}

	public function render( $attr, $instance ) {
		if ( ! is_user_logged_in() ) {
			return '';
		}

		$controller = Templates\Address_List::factory();

		return $controller->render();
	}

}