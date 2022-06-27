<?php


namespace BigCommerce\Cart;

use BigCommerce\Api\v3\ApiException;

/**
 * Class Buy_Now
 *
 * Handles requests from the Buy Now button for products
 */
class Buy_Now extends Add_To_Cart {
	const ACTION = 'buy';

	/**
	 * @param \BigCommerce\Api\v3\Model\Cart $response
	 * @param Cart                           $cart
	 *
	 * @param int                            $post_id
	 * @param int                            $product_id
	 * @param int                            $variant_id
	 *
	 * @return void
	 */
	protected function handle_response( $response, Cart $cart, $post_id, $product_id, $variant_id ) {
		$checkout_url = $cart->get_checkout_url( $response->getId() );
		wp_redirect( $checkout_url, 303 );
		exit();
	}

	/**
	 * @param ApiException $e
	 * @param Cart         $cart
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
		do_action( 'bigcommerce/form/error', $error, $_POST, $cart->get_cart_url() );
	}
}