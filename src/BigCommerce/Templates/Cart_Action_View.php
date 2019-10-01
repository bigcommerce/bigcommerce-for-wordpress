<?php


namespace BigCommerce\Templates;


use BigCommerce\Pages\Cart_Page;

class Cart_Action_View extends Controller {
	const CART = 'cart';
	const HREF = 'href';

	protected $template           = 'components/cart/cart-action-view.php';

	protected function parse_options( array $options ) {
		$defaults = [
			self::CART => [],
		];

		return wp_parse_args( $options, $defaults );
	}

	public function get_data() {
		return [
			self::CART => $this->options[ self::CART ],
			self::HREF => get_permalink( get_option( Cart_Page::NAME ) ),
		];
	}

}
