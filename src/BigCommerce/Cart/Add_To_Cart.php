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
     * Handles the request to add a product to the cart.
     *
     * @param int     $post_id   The ID of the product post.
     * @param CartApi $cart_api  The CartApi instance to interact with the cart API.
     * 
     * @return void
     * @action bigcommerce/action_endpoint/ . self::ACTION
     */
    public function handle_request( $post_id, CartApi $cart_api ) {
        if ( ! $this->validate_request( $post_id, $_POST ) ) {
            $error = new \WP_Error( 'unknown_product', __( 'We were unable to process your request. Please go back and try again.', 'bigcommerce' ) );
            /**
             * Fires when there is an error processing the add to cart form.
             *
             * @param \WP_Error $error    The error object
             * @param array     $submission The submitted form data
             * @param string    $redirect  The URL to redirect to after handling submission
             * @param array     $data     Additional data about the submission
             */
            do_action('bigcommerce/form/error', $error, $_POST, home_url('/'));

            return;
        }

        $product    = new Product( $post_id );
        $product_id = $product->bc_id();
        $variant_id = $this->get_variant_id( $product, $_POST );

        $options = [];

        // Options are sanitized below
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

    /**
     * Sanitizes a product option based on its configuration type.
     *
     * @param mixed $value  The value of the option submitted by the user.
     * @param array $config The configuration of the option (type, id, etc.).
     *
     * @return mixed The sanitized option value.
     */
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
     * Handles the response from the API after attempting to add a product to the cart.
     *
     * @param \BigCommerce\Api\v3\Model\Cart|null $response   The response from the API.
     * @param Cart                                $cart       The cart instance.
     * @param int                                 $post_id    The ID of the product post.
     * @param int                                 $product_id The ID of the product in BigCommerce.
     * @param int                                 $variant_id The variant ID for the product.
     *
     * @return void
     */
    protected function handle_response( $response, Cart $cart, $post_id, $product_id, $variant_id ) {
        $cart_url = $cart->get_cart_url();
        /**
         * Triggered when a form is successfully processed.
         *
         * @param string $message    The message that will display to the user.
         * @param array  $submission The data submitted to the form.
         * @param string $url        The URL to redirect the user to.
         * @param array  $data       Additional data to store with the message.
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
     * Handles exceptions thrown during the cart API process.
     *
     * @param ApiException $e   The exception thrown during the API request.
     * @param Cart         $cart The cart instance.
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
        /**
         * Fires when there is an API error while adding an item to the cart.
         *
         * @param \WP_Error $error      The error object
         * @param array     $submission The submitted form data
         * @param string    $redirect   The URL to redirect to after handling submission
         * @param array     $data      Additional data about the submission
         */
        do_action('bigcommerce/form/error', $error, $_POST, $cart->get_cart_url());
    }

    /**
     * Validates the request to ensure the product exists and is published.
     *
     * @param int   $post_id    The ID of the product post.
     * @param array $submission The form submission data.
     *
     * @return bool True if the request is valid, false otherwise.
     */
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

    /**
     * Gets the product variant ID from the form submission.
     *
     * @param Product $product   The product object.
     * @param array   $submission The form submission data.
     *
     * @return int The variant ID.
     */
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
