<?php


namespace BigCommerce\Templates;


class Cart_Actions extends Controller {
	const CART    = 'cart';
	const ACTIONS = 'actions';

	protected $template = 'components/cart/cart-actions.php';

	protected function parse_options( array $options ) {
		$defaults = [
			self::CART => [],
		];

		return wp_parse_args( $options, $defaults );
	}

	public function get_data() {
		return [
			self::CART    => $this->options[ self::CART ],
			self::ACTIONS => $this->get_actions( $this->options[ self::CART ] ),
		];
	}

	protected function get_actions( $cart ) {
		$checkout = Cart_Action_Checkout::factory( [
			Cart_Action_Checkout::CART => $cart,
		] );
		return [
			'checkout' => $checkout->render(),
		];
	}

}
