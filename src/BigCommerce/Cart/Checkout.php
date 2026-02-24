<?php


namespace BigCommerce\Cart;

use BigCommerce\Api\v3\Api\CartApi;
use BigCommerce\Api_Factory;
use BigCommerce\Pages\Checkout_Page;
use BigCommerce\Settings\Sections\Cart as Cart_Settings;

/**
 * Handles requests during checkout from the Buy Now button for products.
 */
class Checkout {
	/**
	 * The action endpoint for the checkout process.
	 *
	 * @var string
	 */
	const ACTION = 'checkout';

	/**
	 * The factory instance for creating BigCommerce API clients.
	 *
	 * @var Api_Factory
	 */
	private $api_factory;

	/**
	 * Constructor.
	 *
	 * @param Api_Factory $api_factory Factory for creating API clients.
	 */
	public function __construct( Api_Factory $api_factory ) {
		$this->api_factory = $api_factory;
	}

	/**
	 * Handle the request for redirecting to checkout.
	 *
	 * This method processes a checkout request based on the provided cart ID and redirects the user 
	 * to the appropriate checkout page or URL. If the cart ID is missing or an error occurs, 
	 * an error is displayed to the user.
	 *
	 * @param string  $cart_id  The ID of the cart to check out.
	 * @param CartApi $cart_api The BigCommerce Cart API client.
	 *
	 * @return void
	 *
	 * @action bigcommerce/action_endpoint/ . self::ACTION
	 */
	public function handle_request( $cart_id, CartApi $cart_api ) {
		if ( empty( $cart_id ) ) {
			$error = new \WP_Error( 'checkout', __( 'Please add some items to your cart before checking out.', 'bigcommerce' ) );
			/**
			 * Fires when a form error occurs during checkout.
			 *
			 * @param \WP_Error $error      The WordPress error object containing the error code and message
			 * @param array     $submission The submitted form data ($_POST)
			 * @param string    $redirect   The URL to redirect to after error handling
			 */
			do_action( 'bigcommerce/form/error', $error, $_POST, home_url( '/' ) );

			return;
		}

		if ( get_option( Cart_Settings::OPTION_EMBEDDED_CHECKOUT, true ) ) {
			$checkout_page = get_option( Checkout_Page::NAME, 0 );
			if ( $checkout_page ) {
				wp_safe_redirect( get_permalink( $checkout_page ), 303 );
				exit();
			}
		}

		try {
			$redirects    = $cart_api->cartsCartIdRedirectUrlsPost( $cart_id )->getData();
			$checkout_url = $redirects[ 'checkout_url' ];

			/**
			 * This filter is documented in src/BigCommerce/Cart/Cart.php
			 */
			$checkout_url = apply_filters( 'bigcommerce/checkout/url', $checkout_url );

			wp_redirect( $checkout_url, 303 );
			exit();
		} catch ( \Exception $e ) {
			$error = new \WP_Error( 'api_error', __( "We're having some difficulty redirecting you to checkout. Please try again.", 'bigcommerce' ) );
			/**
			 * Fires when a form error occurs during checkout.
			 *
			 * @param \WP_Error $error      The WordPress error object containing the error code and message
			 * @param array     $submission The submitted form data ($_POST)
			 * @param string    $redirect   The URL to redirect to after error handling
			 */
			do_action( 'bigcommerce/form/error', $error, $_POST, home_url( '/' ) );

			return;
		}
	}
}
