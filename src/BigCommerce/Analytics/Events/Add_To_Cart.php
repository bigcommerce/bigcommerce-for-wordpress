<?php


namespace BigCommerce\Analytics\Events;


use BigCommerce\Settings\Sections\Analytics;
use BigCommerce\Templates\Message;
use BigCommerce\Post_Types\Product\Product;

/**
 * Class Add_To_Cart
 *
 * Adds analytics tracking attributes to purchase buttons and success messages.
 *
 * This class is responsible for enhancing the add-to-cart functionality by embedding
 * tracking attributes into the purchase buttons and success messages. The tracking
 * attributes are used to collect analytics data about the user's interactions.
 *
 * @package BigCommerce\Analytics\Events
 */
class Add_To_Cart {

    /**
     * Sets tracking attributes on the success message displayed after adding a product to the cart.
     *
     * This function parses the data provided by the cart action and applies tracking attributes
     * to the success message. The tracking data includes details such as cart ID, product ID,
     * variant ID, and product name.
     *
     * @param array $args Attributes of the success message.
     * @param array $data Data related to the cart action.
     *
     * @return array Modified success message attributes with tracking data.
     */
    public function set_tracking_attributes_on_success_message( $args, $data ) {
        if ( ! array_key_exists( 'data', $data ) ) {
            return $args;
        }

        $data = $data['data'];
        if ( array_key_exists( 'key', $data ) && $data['key'] == 'add_to_cart' ) {
            $data = wp_parse_args( $data, [
                'cart_id'    => '',
                'post_id'    => 0,
                'product_id' => 0,
                'variant_id' => 0,
            ] );

            $track_data = [
                'cart_id'    => $data['cart_id'],
                'post_id'    => $data['post_id'],
                'product_id' => $data['product_id'],
                'variant_id' => $data['variant_id'],
                'name'       => get_the_title( $data['post_id'] ),
            ];
            /**
             * Modifies the tracking data options for analytics.
             *
             * This allows for customization of analytics tracking behavior.
             *
             * @param array $track_data Tracking data options.
             *
             * @return array Modified tracking data options.
             */
            $track_data = apply_filters( Analytics::TRACK_BY_HOOK, $track_data );

            $args[ Message::ATTRIBUTES ] = array_merge( $args[ Message::ATTRIBUTES ], [
                'data-tracking-trigger' => 'ready',
                'data-tracking-event'   => 'add_to_cart_message',
                'data-tracking-data'    => wp_json_encode( $track_data ),
            ] );
        }

        return $args;
    }

    /**
     * Adds tracking attributes to the purchase button for analytics purposes.
     *
     * The tracking attributes include information about the product being purchased, such as
     * product ID, post ID, and product name. This data is embedded into the button's attributes
     * for tracking the add-to-cart action.
     *
     * @param array   $attributes Attributes of the purchase button.
     * @param Product $product    The product object associated with the button.
     *
     * @return array Modified button attributes with tracking data.
     * @filter bigcommerce/button/purchase/attributes
     */
    public function add_tracking_attributes_to_purchase_button( $attributes, $product ) {
        $track_data = [
            'post_id'    => $product->post_id(),
            'product_id' => $product->bc_id(),
            'name'       => get_the_title( $product->post_id() ),
        ];

        $track_data = apply_filters( Analytics::TRACK_BY_HOOK, $track_data );

        return array_merge( $attributes, [
            'data-tracking-trigger' => 'ready',
            'data-tracking-event'   => 'add_to_cart',
            'data-tracking-data'    => wp_json_encode( $track_data ),
        ] );
    }
}
