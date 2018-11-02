<?php


namespace BigCommerce\Cart;

use BigCommerce\Api\v3\Api\CartApi;
use BigCommerce\Api_Factory;
use BigCommerce\Pages\Checkout_Page;
use BigCommerce\Settings\Sections\Cart as Cart_Settings;

/**
 * Class Buy_Now
 *
 * Handles requests from the Buy Now button for products
 */
class Checkout {
	const ACTION = 'checkout';
	private $api_factory;

	public function __construct( Api_Factory $api_factory ) {
		$this->api_factory = $api_factory;
	}

	/**
	 * @param string  $cart_id
	 * @param CartApi $cart_api
	 *
	 * @return void
	 * @action bigcommerce/action_endpoint/ . self::ACTION
	 */
	public function handle_request( $cart_id, CartApi $cart_api ) {
		if ( empty( $cart_id ) ) {
			$error = new \WP_Error( 'checkout', __( 'Please add some items to your cart before checking out.', 'bigcommerce' ) );
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
			$checkout_url = apply_filters( 'bigcommerce/checkout/url', $checkout_url );

			wp_redirect( $checkout_url, 303 );
			exit();
		} catch ( \Exception $e ) {
			$error = new \WP_Error( 'api_error', __( "We're having some difficulty redirecting you to checkout. Please try again.", 'bigcommerce' ) );
			do_action( 'bigcommerce/form/error', $error, $_POST, home_url( '/' ) );

			return;
		}
	}
}