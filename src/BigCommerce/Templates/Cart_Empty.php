<?php


namespace BigCommerce\Templates;


use BigCommerce\Customizer\Sections\Cart as Cart_Settings;
use BigCommerce\Post_Types\Product\Product;

class Cart_Empty extends Controller {
	const CART = 'cart';
	const LINK = 'link_destination';

	protected $template = 'components/cart/cart-empty.php';

	protected function parse_options( array $options ) {
		$defaults = [
			self::CART => [],
		];

		return wp_parse_args( $options, $defaults );
	}

	public function get_data() {
		return [
			self::CART => $this->options[ self::CART ],
			self::LINK => $this->continue_shopping_link_url(),
		];
	}

	private function continue_shopping_link_url() {
		$setting = get_option( Cart_Settings::EMPTY_CART_LINK, Cart_Settings::LINK_HOME );
		switch ( $setting ) {
			case Cart_Settings::LINK_CATALOG:
				$url = get_post_type_archive_link( Product::NAME );
				break;
			case Cart_Settings::LINK_HOME:
			default:
				$url = home_url( '/' );
				break;
		}

		/**
		 * Filter the destination of the Continue Shopping link in an empty cart
		 *
		 * @param string $url
		 */
		return apply_filters( 'bigcommerce/cart/continue_shopping_url', $url );
	}

}
