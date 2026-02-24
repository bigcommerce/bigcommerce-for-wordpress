<?php

namespace BigCommerce\Cart;

use BigCommerce\Api\v3\ApiException;
use BigCommerce\Pages\Checkout_Page;
use BigCommerce\Settings\Sections\Cart as Cart_Settings;

/**
 * Handles requests from the Buy Now button for products.
 * Extends the Add_To_Cart class to reuse functionality for adding products to the cart,
 * with special handling for redirecting the user to the checkout page.
 */
class Buy_Now extends Add_To_Cart {
	const ACTION = 'buy';

	/**
	 * Handles the response after a product has been successfully added to the cart and the purchase process begins.
	 * Redirects the user to the appropriate checkout page, either embedded or external.
	 *
	 * @param \BigCommerce\Api\v3\Model\Cart $response The response from the API after adding the product to the cart.
	 * @param Cart                           $cart The Cart object containing cart data and methods.
	 * @param int                            $post_id The ID of the product being purchased.
	 * @param int                            $product_id The ID of the product being purchased.
	 * @param int                            $variant_id The ID of the selected variant.
	 *
	 * @return void
	 */
	protected function handle_response( $response, Cart $cart, $post_id, $product_id, $variant_id ) {
		if ( get_option( Cart_Settings::OPTION_EMBEDDED_CHECKOUT, true ) ) {
			wp_safe_redirect( esc_url( home_url( '/bigcommerce/checkout/' .  $response->getId() ) ), 303 );
			exit();
		}

		$checkout_url = $cart->get_checkout_url( $response->getId() );
		wp_redirect( $checkout_url, 303 );
		exit();
	}

	/**
	 * Handles exceptions that occur during the purchase process.
	 * Displays a user-friendly error message based on the API exception.
	 *
	 * @param ApiException $e The API exception that occurred during the purchase process.
	 * @param Cart         $cart The Cart object containing cart data.
	 *
	 * @return void
	 */
	protected function handle_exception( ApiException $e, $cart ) {
		if ( strpos( (string) $e->getCode(), '4' ) === 0 ) {
			$body = $e->getResponseBody();
			if ( $body && ! empty( $body->title ) ) {
				$message = sprintf( '[%d] %s', $e->getCode(), $body->title );
			} else {
				$message = $e->getMessage();
			}
			$error = new \WP_Error( 'api_error', sprintf(
				__( 'There was an error purchasing this product. Error message: "%s"', 'bigcommerce' ),
				$message
			), [ 'exception' => [ 'message' => $e->getMessage(), 'code' => $e->getCode() ] ] );
		} else {
			$error = new \WP_Error( 'api_error', __( 'There was an error purchasing this product. It might be out of stock or unavailable.', 'bigcommerce' ), [
				'exception' => [
					'message' => $e->getMessage(),
					'code'    => $e->getCode(),
				],
			] );
		}
		/**
		 * Fires when an error occurs during the Buy Now process.
		 *
		 * @since [VERSION]
		 *
		 * @param \WP_Error $error      The error object containing the error message and details.
		 * @param array     $_POST      The POST data submitted with the request.
		 * @param string    $cart_url   The URL to redirect to if the error needs to be displayed on the cart page.
		 *
		 * @return void
		 */
		do_action( 'bigcommerce/form/error', $error, $_POST, $cart->get_cart_url() );
	}
}
