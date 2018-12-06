<?php

namespace BigCommerce\Amp;

use WP_REST_Request;
use BigCommerce\Settings;
use BigCommerce\Cart\Cart;

/**
 * Class AMP cart.
 */
class Amp_Cart {
	const CHECKOUT_REDIRECT_ACTION = 'amp_checkout';

	/**
	 * Class constructor
	 *
	 * @param string $proxy_base Base path of the proxy REST endpoints.
	 */
	public function __construct( $proxy_base ) {
		$this->proxy_base = $proxy_base;
	}

	/**
	 * Provides a URL endpoint to handle AMP checkout.
	 *
	 * @param int $cart_id Unused.
	 * @return string URL.
	 */
	public function get_checkout_url( $cart_id = null ) {
		return home_url( sprintf( '/bigcommerce/%s', self::CHECKOUT_REDIRECT_ACTION ) );
	}

	/**
	 * Get the cart ID from the cookie
	 *
	 * @return string
	 */
	public function get_cart_id() {
		if ( get_option( Settings\Sections\Cart::OPTION_ENABLE_CART, true ) ) {
			return filter_input( INPUT_COOKIE, Cart::CART_COOKIE, FILTER_SANITIZE_STRING ) ?: false;
		} else {
			return false;
		}
	}

	/**
	 * Gets the URL of the designated cart page.
	 *
	 * @return string URL.
	 */
	public function get_cart_url() {
		$cart_page_id = get_option( Settings\Sections\Cart::OPTION_CART_PAGE_ID, 0 );
		if ( empty( $cart_page_id ) ) {
			$url = home_url( '/' );
		} else {
			$url = amp_get_permalink( $cart_page_id );
		}

		/**
		 * Filter the URL to the cart page
		 *
		 * @param string $url     The URL to the cart page
		 * @param int    $page_id The ID of the cart page
		 */
		return apply_filters( 'bigcommerce/cart/permalink', $url, $cart_page_id );
	}

	/**
	 * Redirects back to the cart page.
	 *
	 * @return void
	 */
	private function back_to_cart() {
		wp_safe_redirect( $this->get_cart_url() );
		die();
	}

	/**
	 * Gets the checkout URL for the current cart and redirects the user there.
	 *
	 * @return void
	 */
	public function handle_redirect_request() {
		$cart_id = $this->get_cart_id();

		if ( empty( $cart_id ) ) {
			$this->back_to_cart();
		}

		$request  = new WP_REST_Request(
			'POST',
			sprintf( '/%scarts/%s/redirect_urls', trailingslashit( $this->proxy_base ), $cart_id )
		);
		$response = rest_do_request( $request );

		if ( 200 !== $response->status || ! isset( $response->data['data']['checkout_url'] ) ) {
			$this->back_to_cart();
		}

		$url  = $response->data['data']['checkout_url'];
		$host = wp_parse_url( $url, PHP_URL_HOST );

		if ( empty( $host ) ) {
			$this->back_to_cart();
		}

		add_filter(
			'allowed_redirect_hosts',
			function( $hosts ) use ( $host ) {
				if ( false !== strpos( $host, 'bigcommerce.com' ) ) {
					$hosts[] = $host;
				}

				return $hosts;
			}
		);

		wp_safe_redirect( $response->data['data']['checkout_url'], 303 );
		die();
	}
}
