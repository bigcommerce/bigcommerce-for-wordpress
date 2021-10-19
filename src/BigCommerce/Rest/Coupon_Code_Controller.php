<?php

namespace BigCommerce\Rest;

use BigCommerce\Api\v3\Api\CartApi;
use BigCommerce\Api\v3\Api\CheckoutApi;
use BigCommerce\Cart\Cart_Mapper;
use BigCommerce\Api\v3\ApiException;

class Coupon_Code_Controller extends Rest_Controller {

	/**
	 * @var CheckoutApi
	 */
	private $checkout_api;

	/**
	 * @var CartApi
	 */
	private $cart_api;

	/**
	 * Rest_Controller constructor.
	 *
	 * @param string       $namespace_base
	 * @param string       $version
	 * @param string       $rest_base
	 */
	public function __construct( $namespace_base, $version, $rest_base, CheckoutApi $checkout_api, CartApi $cart_api ) {
		parent::__construct( $namespace_base, $version, $rest_base );

		$this->checkout_api = $checkout_api;
		$this->cart_api     = $cart_api;
	}

	/**
	 * Add data to the JS config to support cart ajax
	 *
	 * @param array $config
	 *
	 * @return array
	 * @filter bigcommerce/js_config
	 */
	public function js_config( $config ) {
		$config['cart']['coupon_code_add_api_url']    = $this->get_base_url();
		$config['cart']['coupon_code_delete_api_url'] = $this->get_base_url() . '/delete';

		return $config;
	}

	public function register_routes() {
		register_rest_route( $this->namespace, '/' . $this->rest_base, [
			[
				'methods'             => \WP_REST_Server::CREATABLE,
				'callback'            => [ $this, 'apply_coupon_code' ],
				'permission_callback' => [ $this, 'get_permissions_check' ],
				'args'                => [
					'coupon_code' => [
						'type'    => 'string',
						'default' => '',
					]
				],
			],
		] );
		register_rest_route( $this->namespace, '/' . $this->rest_base . '/delete', [
			[
				'methods'             => \WP_REST_Server::CREATABLE,
				'callback'            => [ $this, 'delete_coupon_code' ],
				'permission_callback' => [ $this, 'get_permissions_check' ],
				'args'                => [
					'coupon_code' => [
						'type'    => 'string',
						'default' => '',
					]
				],
			],
        ] );
    }

	/**
	 * Checks if a given request has access to read the rendered shortcodes.
	 *
	 * @param \WP_REST_Request $request Full details about the request.
	 *
	 * @return true|\WP_Error True if the request has read access, WP_Error object otherwise.
	 */
	public function get_permissions_check( $request ) {
		// no access checks for now
		return true;
	}

    /**
	 * @param \WP_REST_Request $request Full details about the request.
	 *
	 * @return \WP_REST_Response|\WP_Error Response object on success, or WP_Error object on failure.
	 *
	 */
	public function apply_coupon_code( $request ) {
		$attributes = $request->get_params();

		if ( empty( $attributes['coupon_code'] ) ) {
			return rest_ensure_response( [
				'error' => __( 'Coupon Code empty.', 'bigcommerce' ),
			] );
		}

		try {
			$response = $this->checkout_api->checkoutsCouponsByCheckoutIdPost( $this->get_cart_id(), [ 'coupon_code' => $attributes['coupon_code'] ] );

			// Checkouts cart response does not match the cart from the Cart api
			// We fetch the cart again so we have a consistent format
			return rest_ensure_response( $this->get_cart() );
		} catch ( ApiException $e ) {
			return rest_ensure_response( [
				'error' => __( 'Your coupon could not be applied to the cart.', 'bigcommerce' ),
			] );
		}

		return [];
	}

	/**
	 * @param \WP_REST_Request $request Full details about the request.
	 *
	 * @return \WP_REST_Response|\WP_Error Response object on success, or WP_Error object on failure.
	 *
	 */
	public function delete_coupon_code( $request ) {
		$attributes = $request->get_params();

		if ( empty( $attributes['coupon_code'] ) ) {
			return rest_ensure_response( [
				'error' => __( 'Coupon Code empty.', 'bigcommerce' ),
			] );
		}

		try {
			$response = $this->checkout_api->checkoutsCouponsByCheckoutIdAndCouponCodeDelete( $this->get_cart_id(), $attributes['coupon_code'] );

			// Checkouts cart response does not match the cart from the Cart api
			// We fetch the cart again so we have a consistent format
			return rest_ensure_response( $this->get_cart() );
		} catch ( ApiException $e ) {
			return rest_ensure_response( [
				'error' => __( 'Your coupon could not be applied to the cart.', 'bigcommerce' ),
			] );
		}

		return [];
	}

	private function format_price( $price ) {
		/**
		 * This filter is documented in src/BigCommerce/Currency/With_Currency.php.
		 */
		return apply_filters( 'bigcommerce/currency/format', sprintf( '¤%0.2f', $price ), $price );
	}

	private function get_cart() {
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
			$cart   = $this->cart_api->cartsCartIdGet( $cart_id, [ 'include' => $include ] )->getData();
			$mapper = new Cart_Mapper( $cart );

			return $mapper->map();
		} catch ( ApiException $e ) {
			return $this->get_empty_cart();
		}
	}

	private function get_cart_id() {
		$cart = new \BigCommerce\Cart\Cart( $this->cart_api );
		return $cart->get_cart_id();
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
