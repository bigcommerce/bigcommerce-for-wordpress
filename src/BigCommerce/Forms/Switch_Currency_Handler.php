<?php


namespace BigCommerce\Forms;


use BigCommerce\Currency\Currency;
use BigCommerce\Api\v3\Api\CartApi;
use BigCommerce\Cart\Cart;
use BigCommerce\Api\v3\Model\CartRequestData;
use BigCommerce\Api\v3\ApiException;
use Bigcommerce\Post_Types\Product\Product;

class Switch_Currency_Handler implements Form_Handler {

	const ACTION = 'switch-currency';

	/**
	 * @var Currency
	 */
	private $currency;

	/**
	 * @var CartApi
	 */
	private $cart_api;

	/**
	 * Switch_Currency_Handler constructor.
	 *
	 * @param Currency $currency
	 */
	public function __construct( Currency $currency, CartApi $cart_api ) {
		$this->currency = $currency;
		$this->cart_api = $cart_api;
	}


	public function handle_request( $submission ) {
		if ( ! $this->should_handle_request( $submission ) ) {
			return;
		}
		
		$errors = $this->validate_submission( $submission );
		
		if ( count( $errors->get_error_codes() ) > 0 ) {
			do_action( 'bigcommerce/form/error', $errors, $submission );

			return;
		}

		$current_code = apply_filters( 'bigcommerce/currency/code', 'USD' );
		if ( $current_code === $submission[ 'bc-currency-code' ] ) {
			return;
		}

		$new_currency_code = $submission[ 'bc-currency-code' ];

		$success = $this->currency->set_currency_code( $new_currency_code );

		if ( ! $success ) {
			return;
		}

		$this->maybe_recreate_cart( $new_currency_code );

		$url = ! empty( $submission['_wp_http_referer'] ) ? parse_url( $submission['_wp_http_referer'] )['path'] : '/';

		/**
		 * The message to display on currency switch
		 *
		 * @param string $message
		 */
		$message = apply_filters( 'bigcommerce/form/currency_switch/success_message', __( 'Currency switched!', 'bigcommerce' ) );

		/**
		 * Triggered when a form is successfully processed.
		 *
		 * @param string $message    The message that will display to the user
		 * @param array  $submission The data submitted with the form
		 * @param string $url        The URL to redirect the user to
		 * @param array  $data       Optional data about the submission
		 */
		do_action( 'bigcommerce/form/success', $message, $submission, $url, [ 'key' => 'currency_switched' ] );
	}

	private function should_handle_request( $submission ) {
		if ( empty( $submission[ 'bc-action' ] ) || $submission[ 'bc-action' ] !== self::ACTION ) {
			return false;
		}
		if ( empty( $submission[ '_wpnonce' ] ) || ! isset( $submission[ 'bc-currency-code' ] ) ) {
			return false;
		}

		return true;
	}

	/**
	 * @param array $submission
	 *
	 * @return \WP_Error
	 */
	private function validate_submission( $submission ) {
		$errors = new \WP_Error();

		if ( ! wp_verify_nonce( $submission[ '_wpnonce' ], self::ACTION ) ) {
			$errors->add( 'invalid_nonce', __( 'There was an error validating your request. Please try again.', 'bigcommerce' ) );
		}

		if ( empty( $submission[ 'bc-currency-code' ] ) ) {
			$errors->add( 'currency_code', __( 'Currency code is required.', 'bigcommerce' ) );
		}

		$errors = apply_filters( 'bigcommerce/form/switch_currency/errors', $errors, $submission );

		return $errors;
	}

	/**
	 * Recreate the cart if it already exists.
	 * Currently it is not possible to update the currency on an already configured cart.
	 *
	 * @param string $new_currency_code
	 * @return void
	 */
	private function maybe_recreate_cart( $new_currency_code ) {
		$cart    = new Cart( $this->cart_api );
		$cart_id = $cart->get_cart_id();

		if ( $cart_id ) {
			try {
				$include = [
					'line_items.physical_items.options',
					'line_items.digital_items.options',
					'redirect_urls',
				];
				$cart_data = $this->cart_api->cartsCartIdGet( $cart_id, [ 'include' => $include ] )->getData();

				$cart->delete_cart();
				
				// Use the new currency code when creating the cart
				$use_new_currency_code = function () use ( $new_currency_code ) {
					return $new_currency_code;
				};
				add_filter( 'bigcommerce/currency/code', $use_new_currency_code, 10, 0 );
				
				// Use the COOKIE global directly to get the cart id
				$use_cart_id_cookie_global = function () {
					return $_COOKIE[ Cart::CART_COOKIE ] ?? null;
				};
				add_filter( 'bigcommerce/cart/cart_id', $use_cart_id_cookie_global, 10, 0 );
				
				$line_items        = $cart_data->getLineItems();
				$line_items_merged = array_merge( $line_items['physical_items'], $line_items['digital_items'] );
				
				foreach ( $line_items_merged as $line_item ) {
					$options = $this->format_item_options( $line_item['options'], $line_item['product_id'] );
					$cart->add_line_item( $line_item['product_id'], $options, $line_item['quantity'] );
				}

				foreach ( $line_items['gift_certificates'] as $line_item ) {
					$cart->add_line_item( $line_item['product_id'], $line_item['options'], $line_item['quantity'] );
				}

				remove_filter( 'bigcommerce/currency/code', $use_new_currency_code, 10, 0 );
				remove_filter( 'bigcommerce/cart/cart_id', $use_cart_id_cookie_global, 10, 0 );
			} catch ( ApiException $e ) {
				
			}
		}
	}

	/**
	 * Format item options to use ids
	 *
	 * @param array $options
	 * @param id $product_id
	 * @return array
	 */
	private function format_item_options( $options, $product_id ) {
		$product = Product::by_product_id( $product_id );
		$product_options = $product->options();

		$mapped = [];
		foreach ( $options as $option ) {
			foreach ( $product_options as $product_option ) {
				if ( $product_option['display_name'] !== $option['name'] ) {
					continue;
				}

				foreach ( $product_option['option_values'] as $option_value ) {
					if ( $option_value['label'] === $option['value'] ) {
						$mapped[ $product_option['id'] ] = $option_value['id'];
					}
				}
			}

		}

		return $mapped;
	}

}