<?php


namespace BigCommerce\Templates;

use BigCommerce\Templates\Shipping_Info_Button;;
use BigCommerce\Customizer\Sections\Cart as Cart_Settings;

class Cart_Footer extends Controller {
	const CART     = 'cart';
	const SUMMARY  = 'summary';
	const ACTIONS  = 'actions';
	const SHIPPING = 'shipping';

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
			self::CART     => $cart,
			self::ACTIONS  => $this->get_actions( $cart ),
			self::SUMMARY  => $this->get_summary( $cart ),
			self::SHIPPING => $this->get_shipping( $cart ),
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
	
	protected function get_shipping( $cart ) {
		$enable_shipping_info = get_option( Cart_Settings::ENABLE_SHIPPING_INFO, false );

		if ( ! $enable_shipping_info || ! $this->is_physical_item_in_cart( $cart ) ) {
			return '';
		}

		$component = Shipping_Info_Button::factory( [] );

		return $component->render();
	}

	protected function is_physical_item_in_cart( $cart ) {
		foreach ( $cart['items'] as $cart_item ) {
			foreach ( $cart_item['bigcommerce_product_type'] as $item_type ) {
				if ( $item_type['slug'] === 'physical' ) {
					return true;
				}
			}
		}

		return false;
	}

}