<?php


namespace BigCommerce\Cart;

use BigCommerce\Api\v3\Api\CartApi;
use BigCommerce\Api\v3\ApiException;
use BigCommerce\Post_Types\Product\Product;

/**
 * Handles requests from the Add to Cart button for products
 */
class Add_To_Cart {
	const ACTION       = 'cart';

	/**
	 * @param int     $post_id
	 * @param CartApi $cart_api
	 *
	 * @return void
	 * @action bigcommerce/action_endpoint/ . self::ACTION
	 */
	public function handle_request( $post_id, CartApi $cart_api ) {
		if ( ! $this->validate_request( $post_id, $_POST ) ) {
			$error = new \WP_Error( 'unknown_product', __( 'We were unable to process your request. Please go back and try again.', 'bigcommerce' ) );
			do_action( 'bigcommerce/form/error', $error, $_POST, home_url( '/' ) );

			return;
		}

		$product    = new Product( $post_id );
		$product_id = $product->bc_id();
		$variant_id = $this->get_variant_id( $product, $_POST );

		$options = [];

		// Options are sanitized bellow
		$submitted_options = empty( $_POST[ 'option' ] ) ? [] : (array) $_POST[ 'option' ]; // phpcs:ignore
		$option_config     = $product->options();
		$modifier_config   = $product->modifiers();
		foreach ( $option_config as $config ) {
			if ( array_key_exists( $config[ 'id' ], $submitted_options ) ) {
				$options[ $config[ 'id' ] ] = absint( $submitted_options[ $config[ 'id' ] ] );
			}
		}
		foreach ( $modifier_config as $config ) {
			if ( array_key_exists( $config[ 'id' ], $submitted_options ) ) {
				$options[ $config[ 'id' ] ] = $this->sanitize_option( $submitted_options[ $config[ 'id' ] ], $config );
			}
		}

		$quantity = array_key_exists( 'quantity', $_POST ) ? absint( $_POST[ 'quantity' ] ) : 1;

		$cart     = new Cart( $cart_api );
		try {
			$response = $cart->add_line_item( $product_id, $options, $quantity );
			$this->handle_response( $response, $cart, $post_id, $product_id, $variant_id );
		} catch ( ApiException $e ) {
			$this->handle_exception( $e, $cart );
		}
	}

	private function sanitize_option( $value, $config ) {
		switch ( $config[ 'type' ] ) {
			case 'date':
				return strtotime( $value );
			case 'multi_line_text':
				return sanitize_textarea_field( $value );
			case 'numbers_only_text':
				return filter_var( $value, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION|FILTER_FLAG_ALLOW_THOUSAND );
			case 'text':
				return sanitize_text_field( $value );
			default: // checkboxes, selects, and radios
				return (int) $value;
		}
	}

	/**
	 * @param \BigCommerce\Api\v3\Model\Cart|null $response
	 * @param Cart                                $cart
	 * @param int                                 $post_id
	 *
	 * @param int                                 $product_id
	 * @param int                                 $variant_id
	 *
	 * @return void
	 */
	protected function handle_response( $response, Cart $cart, $post_id, $product_id, $variant_id ) {
		$cart_url = $cart->get_cart_url();
		/**
		 * Triggered when a form is successfully processed.
		 *
		 * @param string $message    The message that will display to the user
		 * @param array  $submission The data submitted to the form
		 * @param string $url        The URL to redirect the user to
		 * @param array  $data       Additional data to store with the message
		 */
		do_action( 'bigcommerce/form/success', __( '1 item added to Cart', 'bigcommerce' ), $_POST, $cart_url, [
			'key'        => 'add_to_cart',
			'cart_id'    => $cart->get_cart_id(),
			'post_id'    => $post_id,
			'product_id' => $product_id,
			'variant_id' => $variant_id,
		] );
		wp_safe_redirect( esc_url_raw( $cart_url ), 303 );
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
				__( 'There was an error adding this product to your cart. Error message: "%s"', 'bigcommerce' ),
				$message
			), [ 'exception' => [ 'message' => $e->getMessage(), 'code' => $e->getCode() ] ] );
		} else {
			$error = new \WP_Error( 'api_error', __( 'There was an error adding this product to your cart. It might be out of stock or unavailable.', 'bigcommerce' ), [
				'exception' => [
					'message' => $e->getMessage(),
					'code'    => $e->getCode(),
				],
			] );
		}
		do_action( 'bigcommerce/form/error', $error, $_POST, $cart->get_cart_url() );
	}

	protected function validate_request( $post_id, $submission ) {
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

	protected function get_variant_id( Product $product, $submission ) {
		if ( ! empty( $submission[ 'variant_id' ] ) ) {
			return (int) $submission[ 'variant_id' ];
		}

		$data = $product->get_source_data();

		foreach ( $data->variants as $variant ) {
			foreach ( $variant->option_values as $option ) {
				$key = 'option-' . $option->option_id;
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
