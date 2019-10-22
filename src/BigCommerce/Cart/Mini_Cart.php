<?php

namespace BigCommerce\Cart;

use BigCommerce\Pages\Cart_Page;
use BigCommerce\Pages\Checkout_Page;

class Mini_Cart {

	/**
	 * @param array $config
	 *
	 * @return array
	 * @filter bigcommerce/js_config
	 */
	public function add_mini_cart_config( $config ) {
		$config['cart']['mini_cart'] = [
			'enabled' => $this->mini_cart_enabled(),
		];

		return $config;
	}

	private function mini_cart_enabled() {
		$enabled = true;
		if ( ! get_option( \BigCommerce\Settings\Sections\Cart::OPTION_ENABLE_CART, true ) ) {
			$enabled = false;
		}

		if ( get_option( \BigCommerce\Customizer\Sections\Cart::ENABLE_MINI_CART, '' ) !== 'yes' ) {
			$enabled = false;
		}

		if ( is_page( get_option( Cart_Page::NAME, 0 ) ) || is_page( get_option( Checkout_Page::NAME, 0 ) ) ) {
			$enabled = false;
		}

		/**
		 * Filter whether the mini-cart widget should be enabled on the current page
		 *
		 * @param bool $enabled Whether the mini-cart is enabled
		 */
		return apply_filters( 'bigcommerce/cart/mini-cart-enabled', $enabled );
	}
}
