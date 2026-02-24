<?php

namespace BigCommerce\Compatibility\WooCommerce;

use BigCommerce\Cart\Cart as BC_Cart;
use BigCommerce\Api\v3\ApiException;
use BigCommerce\Api\v3\Api\CartApi;
use BigCommerce\Cart\Cart_Mapper;

/**
 * This class handles the interaction between the WooCommerce cart and BigCommerce's cart API.
 * It retrieves cart information, including the cart subtotal, and maps it to a WooCommerce-compatible format.
 *
 * @package BigCommerce
 * @subpackage Compatibility\WooCommerce
 */
class Cart {

	/**
	 * The count of items in the cart.
	 *
	 * @var int
	 */
	public $cart_contents_count = 0;

	/** 
	 * The instance of BigCommerce CartApi for interacting with the BigCommerce cart.
	 *
	 * @var CartApi
	 */
	private $bc_cart_api;

	/**
	 * Cart constructor.
	 *
	 * @param CartApi $bc_cart_api The BigCommerce CartApi instance used to interact with the BigCommerce cart.
	 */
	public function __construct( CartApi $bc_cart_api )
	{
		$this->bc_cart_api         = $bc_cart_api;
		$this->cart_contents_count = filter_input( INPUT_COOKIE, BC_Cart::COUNT_COOKIE, FILTER_SANITIZE_NUMBER_INT ) ?: 0;
	}

	/**
	 * Get the subtotal for the current cart.
	 *
	 * @return string|int The formatted subtotal of the cart or 0 if the cart amount is unavailable.
	 */
	public function get_cart_subtotal() {
		$bc_cart = $this->get_bc_cart();
		
		if ( isset( $bc_cart['cart_amount'] ) ) {
			return $bc_cart['cart_amount']['formatted'];
		}

		return 0;
	}

	private function get_bc_cart() {
		$bc_cart_id  = $this->get_bc_cart_id();

		if ( empty( $bc_cart_id ) ) {
			return $this->get_empty_bc_cart();
		}
		
		try {
			$include = [
				'line_items.physical_items.options',
				'line_items.digital_items.options',
				'redirect_urls',
			];
			$cart   = $this->bc_cart_api->cartsCartIdGet( $bc_cart_id, [ 'include' => $include ] )->getData();
			$mapper = new Cart_Mapper( $cart );

			return $mapper->map();
		} catch ( ApiException $e ) {
			return $this->get_empty_bc_cart();
		}

	}

	private function get_bc_cart_id() {
		$bc_cart = new BC_Cart( $this->bc_cart_api );
		return $bc_cart->get_cart_id();
	}

	private function get_empty_bc_cart() {
		return [
			'cart_id'         => '',
			'base_amount'     => 0,
			'discount_amount' => 0,
			'cart_amount'     => 0,
			'items'           => [],
		];
	}

}