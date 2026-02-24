<?php


namespace BigCommerce\Analytics\Events;


use BigCommerce\Post_Types\Product\Product;
use BigCommerce\Settings\Sections\Analytics;
use BigCommerce\Templates\Product_Title;
use BigCommerce\Templates\View_Product_Button;

/**
 * Class View_Product
 *
 * Adds analytics events to product permalink and buttons for tracking.
 *
 * This class is responsible for attaching analytics tracking attributes to the product's view
 * buttons and permalink. It tracks events such as product views by adding the necessary data
 * attributes to the HTML elements. The tracking includes product information like product ID,
 * name, and SKU if enabled in the settings.
 *
 * @package BigCommerce\Analytics\Events
 */
class View_Product {
    
    /**
     * Adds tracking attributes to the product view button.
     *
     * This function embeds tracking data into the attributes of the product view button.
     * The tracking attributes include product details like product ID, post ID, and product name.
     * It triggers the 'view_product' event when the button is clicked.
     *
     * @param array  $options Options for the button.
     * @param string $template The template name for the button.
     *
     * @return array Modified options with added tracking attributes.
     * @filter bigcommerce/template=components/products/view-product-button.php/options
     */
    public function add_tracking_attributes_to_button( $options = [], $template = '' ) {
        if ( empty( $options[ View_Product_Button::PRODUCT ] ) ) {
            return $options;
        }

        /** @var Product $product */
        $product = $options[ View_Product_Button::PRODUCT ];

        if ( empty( $options[ View_Product_Button::ATTRIBUTES ] ) ) {
            $options[ View_Product_Button::ATTRIBUTES ] = [];
        }

        $track_data = [
            'post_id'    => $product->post_id(),
            'product_id' => $product->bc_id(),
            'name'       => get_the_title( $product->post_id() ),
        ];

        $track_data = apply_filters( Analytics::TRACK_BY_HOOK, $track_data );

        $options[ View_Product_Button::ATTRIBUTES ] = array_merge( $options[ View_Product_Button::ATTRIBUTES ], [
            'data-tracking-trigger' => 'click',
            'data-tracking-event'   => 'view_product',
            'data-tracking-data'    => wp_json_encode( $track_data ),
        ] );

        return $options;
    }

    /**
     * Adds tracking attributes to the product permalink.
     *
     * This function attaches tracking data to the product permalink's attributes.
     * The tracking includes product details such as product ID, post ID, and product name.
     * The 'view_product' event is triggered when the permalink is clicked.
     *
     * @param array  $options Options for the permalink.
     * @param string $template The template name for the permalink.
     *
     * @return array Modified options with added tracking attributes.
     * @filter bigcommerce/template=components/products/product-title.php/options
     */
    public function add_tracking_attributes_to_permalink( $options, $template ) {
        if ( empty( $options[ Product_Title::USE_PERMALINK ] ) || empty( $options[ Product_Title::PRODUCT ] ) ) {
            return $options;
        }

        if ( empty( $options[ View_Product_Button::ATTRIBUTES ] ) ) {
            $options[ View_Product_Button::ATTRIBUTES ] = [];
        }

        $product = $options[ Product_Title::PRODUCT ];

        $track_data = [
            'post_id'    => $product->post_id(),
            'product_id' => $product->bc_id(),
            'name'       => get_the_title( $product->post_id() ),
        ];

        $track_data = apply_filters( Analytics::TRACK_BY_HOOK, $track_data );

        $options[ Product_Title::LINK_ATTRIBUTES ] = array_merge( $options[ Product_Title::LINK_ATTRIBUTES ], [
            'data-tracking-trigger' => 'click',
            'data-tracking-event'   => 'view_product',
            'data-tracking-data'    => wp_json_encode( $track_data ),
        ] );

        return $options;
    }

    /**
     * Updates tracking data to include SKU information if enabled in Analytics settings.
     *
     * If SKU tracking is enabled in the Analytics options, this function appends the SKU
     * to the tracking data for the product. If a variant ID is provided, the variant's SKU
     * is also included. If the product cannot be loaded, the original tracking data is returned.
     *
     * @param array $track_data The existing tracking data.
     *
     * @return array The updated tracking data, potentially including SKU information.
     */
    public function change_track_data( $track_data ) {
        $should_track_sku = (bool) get_option( Analytics::TRACK_PRODUCT_SKU, 0 );

        if ( empty( $should_track_sku ) ) {
            return $track_data;
        }

        try {
            $product           = new Product( $track_data['post_id'] );
            $track_data['sku'] = $product->get_property( 'sku' );

            if ( ! empty( $track_data['variant_id'] ) ) {
                $track_data['variant_sku'] = $product->get_variant_sku( $track_data['variant_id'] );
            }

            return $track_data;
        } catch ( \Exception $exception ) {
            return $track_data;
        }
    }
}
