<?php


namespace BigCommerce\Shortcodes;

use \BigCommerce\Templates;

class Address_List implements Shortcode {

	const NAME = 'bigcommerce_shipping_address_list';


	public function __construct() {
	}

	public function render( $attr, $instance ) {
		$controller = new Templates\Address_List();

		return $controller->render();
	}

}