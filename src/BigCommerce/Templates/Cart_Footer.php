<?php


namespace BigCommerce\Templates;


class Cart_Footer extends Controller {
	const CART    = 'cart';
	const SUMMARY = 'summary';
	const ACTIONS = 'actions';

	protected $template = 'components/cart/cart-footer.php';
	protected $wrapper_tag = 'footer';
	protected $wrapper_classes = [ 'bc-cart-footer' ];

	protected function parse_options( array $options ) {
		$defaults = [
			self::CART => [],
		];

		return wp_parse_args( $options, $defaults );
	}

	public function get_data() {
		$cart = $this->options[ self::CART ];

		return [
			self::CART    => $cart,
			self::ACTIONS => $this->get_actions( $cart ),
			self::SUMMARY => $this->get_summary( $cart ),
		];
	}

	protected function get_actions( $cart ) {
		$component = Cart_Actions::factory( [
			Cart_Actions::CART => $cart,
		] );

		return $component->render();
	}

	protected function get_summary( $cart ) {
		$component = Cart_Summary::factory( [
			Cart_Summary::CART => $cart,
		] );

		return $component->render();
	}

}