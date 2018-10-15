<?php

namespace BigCommerce\Shortcodes;

use BigCommerce\Api\v3\Api\CartApi;
use BigCommerce\Api\v3\ApiException;
use BigCommerce\Cart\Add_To_Cart;
use BigCommerce\Customizer\Styles;
use BigCommerce\Templates;

class Checkout implements Shortcode {
	const NAME = 'bigcommerce_checkout';

	/** @var CartApi */
	private $cart_api;

	/** @var Styles */
	private $styles;

	/**
	 * @param CartApi $cart_api
	 * @param Styles $styles
	 */
	public function __construct( CartApi $cart_api, Styles $styles ) {
		$this->cart_api = $cart_api;
		$this->styles = $styles;
	}

	/**
	 * @inheritdoc
	 */
	public function render( $attr, $instance ) {
		$cart_id = $this->get_cart_id();

		if ( !empty($cart_id) ) {
			$attr = shortcode_atts( [], $attr, self::NAME );
			$controller = new Templates\Checkout( [
				Templates\Checkout::EMBEDDED_CHECKOUT_URL => $this->get_embedded_checkout_url($cart_id),
				Templates\Checkout::STYLES => isset($attr['styles']) ? $attr['styles'] : json_encode($this->styles->get_checkout_styles()),
			] );
		} else {
			$controller = new Templates\Cart_Empty();
		}

		return $controller->render();
	}

	/**
	 * @return string
	 */
	private function get_cart_id() {
		return isset( $_COOKIE[ Add_To_Cart::CART_COOKIE ] ) ? $_COOKIE[ Add_To_Cart::CART_COOKIE ] : '';
	}

	/**
	 * @param string $cart_id
	 * @return string|null
	 */
	private function get_embedded_checkout_url( $cart_id ) {
		try {
			$checkout_url = $this->cart_api->cartsCartIdRedirectUrlsPost($cart_id)->getData()->getEmbeddedCheckoutUrl();

			return apply_filters( 'bigcommerce/checkout/url', $checkout_url );
		} catch ( ApiException $e ) {
			return null;
		}
	}
}
