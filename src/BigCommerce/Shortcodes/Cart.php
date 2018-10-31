<?php


namespace BigCommerce\Shortcodes;


use BigCommerce\Api\v3\ApiException;
use BigCommerce\Api\v3\Api\CartApi;
use BigCommerce\Cart\Add_To_Cart;
use BigCommerce\Cart\Cart_Mapper;
use BigCommerce\Templates;

class Cart implements Shortcode {

	const NAME = 'bigcommerce_cart';

	/** @var CartApi */
	private $cart_api;

	public function __construct( CartApi $cart_api ) {
		$this->cart_api     = $cart_api;
	}

	public function render( $attr, $instance ) {
		if ( ( (bool) get_option( \BigCommerce\Settings\Sections\Cart::OPTION_ENABLE_CART, true ) ) == false ) {
			return ''; // render nothing if the cart is disabled
		}

		$attr = shortcode_atts( [
		], $attr, self::NAME );

		$cart = $this->get_cart( $attr );

		if ( count( $cart[ 'items' ] ) > 0 ) {
			$controller = Templates\Cart::factory( [
				Templates\Cart::CART => $cart,
			] );
		} else {
			$controller = Templates\Cart_Empty::factory( [
				Templates\Cart::CART => $cart,
			] );
		}

		return $controller->render();
	}

	private function get_cart( $attr ) {
		$cart_id = $this->get_cart_id();
		if ( empty( $cart_id ) ) {
			return $this->get_empty_cart();
		}
		try {
			$include = [
				'line_items.physical_items.options',
				'line_items.digital_items.options',
				'redirect_urls',
			];
			$cart   = $this->cart_api->cartsCartIdGet( $cart_id, $include )->getData();
			$mapper = new Cart_Mapper( $cart );

			return $mapper->map();
		} catch ( ApiException $e ) {
			return $this->get_empty_cart();
		}
	}

	private function get_cart_id() {
		return isset( $_COOKIE[ Add_To_Cart::CART_COOKIE ] ) ? $_COOKIE[ Add_To_Cart::CART_COOKIE ] : '';
	}

	private function get_empty_cart() {
		return [
			'cart_id'         => '',
			'base_amount'     => 0,
			'discount_amount' => 0,
			'cart_amount'     => 0,
			'items'           => [],
		];
	}


}