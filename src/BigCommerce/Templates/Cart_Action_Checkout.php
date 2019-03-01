<?php


namespace BigCommerce\Templates;


class Cart_Action_Checkout extends Controller {
	const CART = 'cart';

	protected $template           = 'components/cart/cart-action-checkout.php';
	protected $wrapper_tag        = 'button';
	protected $wrapper_classes    = [ 'bc-btn', 'bc-cart-actions__checkout-button' ];
	protected $wrapper_attributes = [ 'data-js' => 'proceed-to-checkout', 'type' => 'submit' ];

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