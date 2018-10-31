<?php


namespace BigCommerce\Templates;


class Order_Not_Found extends Controller {
	protected $template = 'components/orders/order-not-found.php';

	protected function parse_options( array $options ) {
		return $options;
	}

	public function get_data() {
		return [];
	}

}