<?php


namespace BigCommerce\Cart;

/**
 * Class Buy_Now
 *
 * Handles requests from the Buy Now button for products
 */
class Buy_Now extends Add_To_Cart {
	const ACTION = 'buy';

	/**
	 * @param \BigCommerce\Api\v3\Model\Cart|null $response
	 * @param Cart                                $cart
	 *
	 * @param int                                 $post_id
	 * @param int                                 $product_id
	 * @param int                                 $variant_id
	 *
	 * @return void
	 */
	protected function handle_response( $response, Cart $cart, $post_id, $product_id, $variant_id ) {
		$checkout_url = $response ? $cart->get_checkout_url( $response->getId() ) : '';
		if ( $checkout_url ) {
			wp_redirect( $checkout_url, 303 );
			exit();
		} else {
			$error = new \WP_Error( 'api_error', $this->error_message() );
			do_action( 'bigcommerce/form/error', $error, $_POST, home_url( '/' ) );
		}
	}

	protected function error_message() {
		return __( 'There was an error purchasing this product. It might be out of stock or unavailable.', 'bigcommerce' );
	}
}