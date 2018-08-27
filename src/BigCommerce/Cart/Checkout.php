<?php


namespace BigCommerce\Cart;

use BigCommerce\Accounts\Login;
use Bigcommerce\Api\Client;
use BigCommerce\Api\v3\Api\CartApi;
use BigCommerce\Api_Factory;

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

	/**
	 * @param string $checkout_url
	 *
	 * @return string
	 * @filter bigcommerce/checkout/url
	 */
	public function set_login_token_for_checkout( $checkout_url ) {
		$customer_id = (int) ( is_user_logged_in() ? get_user_option( Login::CUSTOMER_ID_META, get_current_user_id() ) : 0 );
		if ( $customer_id ) {
			try {
				$store_api = $this->api_factory->store();
				$store = $store_api->getStore();
				$token = $store_api->getCustomerLoginToken( $customer_id, $checkout_url );
				$checkout_url = $store->secure_url . '/login/token/' . $token;
			} catch ( \Exception $e ) {
				return $checkout_url;
			}
		}

		return $checkout_url;
	}
}