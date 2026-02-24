<?php


namespace BigCommerce\Forms;


use BigCommerce\Currency\Currency;
use BigCommerce\Api\v3\Api\CartApi;
use BigCommerce\Cart\Cart;
use BigCommerce\Api\v3\Model\CartRequestData;
use BigCommerce\Api\v3\ApiException;
use Bigcommerce\Post_Types\Product\Product;

/**
 * Handles the logic for switching currencies in the BigCommerce cart and triggers success/error actions.
 * This class interacts with the currency management system and the cart API to update the currency and 
 * recreate the cart if needed, ensuring the correct display and pricing in the switched currency.
 *
 * @package BigCommerce\Forms
 */
class Switch_Currency_Handler implements Form_Handler {

    /**
     * Action name for switching currency.
     *
     * @var string
     */
    const ACTION = 'switch-currency';

    /**
     * The currency management object.
     *
     * @var Currency
     */
    private $currency;

    /**
     * The API client for managing carts.
     *
     * @var CartApi
     */
    private $cart_api;

    /**
     * Switch_Currency_Handler constructor.
     *
     * Initializes the handler with the required dependencies: the currency management object and the cart API client.
     *
     * @param Currency $currency The currency management object.
     * @param CartApi  $cart_api The API client for managing carts.
     */
    public function __construct( Currency $currency, CartApi $cart_api ) {
        $this->currency = $currency;
        $this->cart_api = $cart_api;
    }

    /**
     * Handles the form submission for switching currency.
     *
     * Validates the form submission, switches the currency, and recreates the cart if necessary.
     * If the submission is valid, it triggers the relevant success action or error handling hooks.
     *
     * @param array $submission The submitted form data.
     *
     * @return void
     */
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
         * @param string $message    The success message to display to the user.
         * @param array  $submission The submitted form data.
         * @param string $url        The URL to redirect the user to.
         * @param array  $data       Optional additional data about the submission.
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
     * Validates the form submission data.
     *
     * Ensures the nonce is valid and the required currency code is provided.
     *
     * @param array $submission The submitted form data.
     *
     * @return \WP_Error The validation errors, if any.
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
     * Recreates the cart if one exists and updates the currency.
     *
     * Since it is not possible to update the currency on an already configured cart,
     * this method deletes the existing cart and recreates it with the new currency code.
     *
     * @param string $new_currency_code The new currency code to use for the cart.
     *
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
     * Formats the item options for the cart line item.
     *
     * Converts product options to IDs based on the product's available options.
     *
     * @param array $options   The options to format.
     * @param int   $product_id The product ID.
     *
     * @return array The formatted options.
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