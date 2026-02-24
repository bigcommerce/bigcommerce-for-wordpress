<?php

namespace BigCommerce\Import\Importers\Products;

use BigCommerce\Api\v3\Api\CatalogApi;
use BigCommerce\Api\v3\Model;
use BigCommerce\Post_Types\Product\Product;
use BigCommerce\Import\Importers\Products\Product_Builder;

/**
 * Handles the creation and saving of BigCommerce products into WordPress.
 */
class Product_Creator extends Product_Saver {
    /**
     * Executes the import process for a product.
     *
     * @return mixed The result of the parent import process.
     */
    public function do_import() {
        $this->create_default_post();
        return parent::do_import();
    }

    private function create_default_post() {
        $this->post_id = wp_insert_post([
			'post_title'  => __( 'Auto Draft' ),
			'post_type'   => Product::NAME,
			'post_status' => 'auto-draft',
        ]);
    }

    /**
     * Retrieves the array of post data to be saved in the WordPress database.
     *
     * Adds a default comment status if it is not already set.
     *
     * @param Product_Builder $builder The product builder instance containing the product data.
     *
     * @return array The post data array.
     */
    protected function get_post_array( Product_Builder $builder ) {
        $postarr = parent::get_post_array( $builder );
        if ( ! array_key_exists( 'comment_status', $postarr ) ) {
            $postarr[ 'comment_status' ] = get_default_comment_status( Product::NAME );
        }

        return $postarr;
    }

    /**
     * Sends notifications after a product has been created via the import process.
     *
     * Triggers a custom action hook for other processes to react to the product creation event.
     *
     * @return void
     */
    protected function send_notifications() {
        /**
         * Fires after a product has been created during the import process.
         *
         * @param int           $post_id The WordPress post ID of the created product.
         * @param Model\Product $product The product data from the BigCommerce API.
         * @param Model\Listing $listing The channel listing data from the BigCommerce API.
         * @param CatalogApi    $catalog The Catalog API instance used for the import.
         */
        do_action( 'bigcommerce/import/product/created', $this->post_id, $this->product, $this->listing, $this->catalog );
        parent::send_notifications();
    }
}