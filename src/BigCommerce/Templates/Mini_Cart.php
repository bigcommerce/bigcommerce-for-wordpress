<?php


namespace BigCommerce\Templates;


class Mini_Cart extends Controller {
	const CART   = 'cart';
	const HEADER = 'header';
	const ITEMS  = 'items';
	const FOOTER = 'footer';

	protected $template = 'components/cart/mini-cart.php';

	protected function parse_options( array $options ) {
		$defaults = [
			self::CART => [],
		];

		return wp_parse_args( $options, $defaults );
	}

	public function get_data() {
		$cart = $this->options[ self::CART ];

		return [
			self::CART   => $cart,
			self::HEADER => $this->get_header(),
			self::ITEMS  => $this->get_items( $cart ),
			self::FOOTER => $this->get_footer( $cart ),
		];
	}

	protected function get_header() {
		$component = Mini_Cart_Header::factory( [] );

		return $component->render();
	}

	protected function get_items( $cart ) {
		$component = Mini_Cart_Items::factory( [
			Mini_Cart_Items::CART => $cart,
		] );

		return $component->render();
	}

	protected function get_footer( $cart ) {
		$component = Mini_Cart_Footer::factory( [
			Mini_Cart_Footer::CART => $cart,
		] );

		return $component->render();
	}

}
