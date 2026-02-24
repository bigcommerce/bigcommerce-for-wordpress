<?php

namespace BigCommerce\Amp;

use WP_REST_Request;
use BigCommerce\Settings;
use BigCommerce\Cart\Cart;

/**
 * Class Amp_Cart
 *
 * Handles AMP-specific cart functionalities such as retrieving cart details,
 * generating checkout URLs, and managing cart-related redirects.
 *
 * @package BigCommerce\Amp
 */
class Amp_Cart {
    /**
     * Action identifier for AMP checkout redirection.
     *
     * @var string
     */
    const CHECKOUT_REDIRECT_ACTION = 'amp_checkout';

    /**
     * Base path of the proxy REST endpoints.
     *
     * @var string
     */
    private $proxy_base;

    /**
     * Class constructor.
     *
     * @param string $proxy_base Base path of the proxy REST endpoints.
     */
    public function __construct( $proxy_base ) {
        $this->proxy_base = $proxy_base;
    }

    /**
     * Provides a URL endpoint to handle AMP checkout.
     *
     * @param int|null $cart_id Unused in this implementation.
     * @return string URL to the AMP checkout endpoint.
     */
    public function get_checkout_url( $cart_id = null ) {
        return home_url( sprintf( '/bigcommerce/%s', self::CHECKOUT_REDIRECT_ACTION ) );
    }

    /**
     * Retrieves the cart ID from the browser's cookie.
     *
     * Checks if the cart functionality is enabled and retrieves the cart ID
     * from the cookie set by BigCommerce.
     *
     * @return string|false Cart ID if available, false otherwise.
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
     * Determines the URL of the cart page based on the WordPress option
     * for the cart page ID. Falls back to the home URL if no cart page is set.
     *
     * @return string URL of the cart page.
     */
    public function get_cart_url() {
        $cart_page_id = get_option( Settings\Sections\Cart::OPTION_CART_PAGE_ID, 0 );

        if ( empty( $cart_page_id ) ) {
            $url = home_url( '/' );
        } else {
            $url = amp_get_permalink( $cart_page_id );
        }

        /**
         * Filter the URL to the cart page.
         *
         * @param string $url     The URL to the cart page.
         * @param int    $page_id The ID of the cart page.
         */
        return apply_filters( 'bigcommerce/cart/permalink', $url, $cart_page_id );
    }

    /**
     * Redirects the user back to the cart page.
     *
     * Sends an HTTP redirect to the cart page and terminates script execution.
     *
     * @return void
     */
    private function back_to_cart() {
        wp_safe_redirect( $this->get_cart_url() );
        die();
    }

    /**
     * Handles redirection to the checkout URL for the current cart.
     *
     * Uses the cart ID to generate a checkout URL via the BigCommerce API.
     * If the checkout URL cannot be retrieved, redirects back to the cart page.
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
