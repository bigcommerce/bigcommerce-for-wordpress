<?php


namespace BigCommerce\Cart;

use BigCommerce\Accounts\Login;
use Bigcommerce\Api\Client;
use BigCommerce\Api\v3\ApiException;
use BigCommerce\Api\v3\CartApi;
use BigCommerce\Api\v3\Model\CartRequestData;
use BigCommerce\Api\v3\Model\LineItemRequestData;
use BigCommerce\Post_Types\Product\Product;

/**
 * Class Buy_Now
 *
 * Handles requests from the Buy Now button for products
 */
class Buy_Now {
	const ACTION = 'buy';

	/**
	 * @param int     $post_id
	 * @param CartApi $cart_api
	 *
	 * @return void
	 * @action bigcommerce/action_endpoint/ . self::ACTION
	 */
	public function handle_request( $post_id, CartApi $cart_api ) {
		if ( ! $this->validate_request( $post_id, $_POST ) ) {
			$error = new \WP_Error( 'unknown_product', __( 'There was an error purchasing this product. It might be out of stock or unavailable.', 'bigcommerce' ) );
			do_action( 'bigcommerce/form/error', $error, $_POST, home_url( '/' ) );
			return;
		}

		$product = new Product( $post_id );

		$product_id = $product->bc_id();
		$variant_id = $this->get_variant_id( $product, $_POST );

		$request_data = new CartRequestData();
		$request_data->setLineItems( [
			new LineItemRequestData( [
				'quantity'   => 1,
				'product_id' => $product_id,
				'variant_id' => $variant_id,
			] ),
		] );
		$request_data->setGiftCertificates( [] );
		try {
			$customer_id = (int) ( is_user_logged_in() ? get_user_option( Login::CUSTOMER_ID_META, get_current_user_id() ) : 0 );
			if ( $customer_id ) {
				$request_data->setCustomerId( $customer_id );
			}
			$cart         = $cart_api->cartsPost( $request_data )->getData();
			$cart_id      = $cart->getId();
			$redirects    = $cart_api->cartsCartIdRedirectUrlsPost( $cart_id )->getData();
			$checkout_url = $redirects[ 'checkout_url' ];
			$checkout_url = apply_filters( 'bigcommerce/checkout/url', $checkout_url );

			wp_redirect( $checkout_url, 303 );
			exit();
		} catch ( \Exception $e ) {
			$error = new \WP_Error( 'api_error', __( 'There was an error purchasing this product. It might be out of stock or unavailable.', 'bigcommerce' ) );
			do_action( 'bigcommerce/form/error', $error, $_POST, home_url( '/' ) );
		}
	}

	private function validate_request( $post_id, $submission ) {
		$post = get_post( $post_id );
		if ( empty( $post ) ) {
			return false;
		}
		if ( Product::NAME !== $post->post_type ) {
			return false;
		}
		if ( $post->post_status !== 'publish' ) {
			return false;
		}

		return true;
	}

	private function get_variant_id( Product $product, $submission ) {
		if ( ! empty( $submission[ 'variant_id' ] ) ) {
			return $submission[ 'variant_id' ];
		}

		$data = $product->get_source_data();

		foreach ( $data->variants as $variant ) {
			foreach ( $variant->option_values as $option ) {
				$key = 'option-' . $option->option_id ;
				if ( ! isset( $submission[ $key ] ) ) {
					continue 2;
				}
				if ( $submission[ $key ] != $option->id ) {
					continue 2;
				}
			}
			// all options matched, we have a winner
			return $variant->id;
		}

		// fall back to the first variant
		if ( ! empty( $data->variants ) ) {
			return reset( $data->variants )->id;
		}

		return 0;
	}
}