<?php


namespace BigCommerce\Templates;


class Cart_Header extends Controller {
	protected $template = 'components/cart/cart-header.php';


	protected function parse_options( array $options ) {
		$defaults = [];

		return wp_parse_args( $options, $defaults );
	}

	public function get_data() {
		return [];
	}

}