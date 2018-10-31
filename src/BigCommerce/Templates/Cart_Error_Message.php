<?php


namespace BigCommerce\Templates;


class Cart_Error_Message extends Controller {
	const MESSAGE  = 'message';

	protected $template = 'components/cart/cart-error-message.php';


	protected function parse_options( array $options ) {
		$defaults = [];

		return wp_parse_args( $options, $defaults );
	}

	public function get_data() {
		return [];
	}

}