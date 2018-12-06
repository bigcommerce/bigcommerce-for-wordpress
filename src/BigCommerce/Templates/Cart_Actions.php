<?php


namespace BigCommerce\Templates;


class Cart_Actions extends Controller {
	const CART = 'cart';

	protected $template = 'components/cart/cart-actions.php';

	protected function parse_options( array $options ) {
		$defaults = [
			self::CART => [],
		];

		return wp_parse_args( $options, $defaults );
	}

	public function get_data() {
		return [
			self::CART => $this->options[ self::CART ],
		];
	}

}