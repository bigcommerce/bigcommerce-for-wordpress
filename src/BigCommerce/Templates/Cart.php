<?php


namespace BigCommerce\Templates;


class Cart extends Controller {
	const CART          = 'cart';
	const ERROR_MESSAGE = 'error_message';
	const HEADER        = 'header';
	const ITEMS         = 'items';
	const FOOTER        = 'footer';

	protected $template = 'components/cart/cart.php';

	protected function parse_options( array $options ) {
		$defaults = [
			self::CART => [],
		];

		return wp_parse_args( $options, $defaults );
	}

	public function get_data() {
		$cart = $this->options[ self::CART ];

		return [
			self::CART          => $cart,
			self::ERROR_MESSAGE => $this->get_error_message(),
			self::HEADER        => $this->get_header(),
			self::ITEMS         => $this->get_items( $cart ),
			self::FOOTER        => $this->get_footer( $cart ),
		];
	}

	protected function get_error_message() {
		$component = Cart_Error_Message::factory( [] );

		return $component->render();
	}

	protected function get_header() {
		$component = Cart_Header::factory( [] );

		return $component->render();
	}

	protected function get_items( $cart ) {
		$component = Cart_Items::factory( [
			Cart_Items::CART => $cart,
		] );

		return $component->render();
	}

	protected function get_footer( $cart ) {
		$component = Cart_Footer::factory( [
			Cart_Footer::CART => $cart,
		] );

		return $component->render();
	}

}