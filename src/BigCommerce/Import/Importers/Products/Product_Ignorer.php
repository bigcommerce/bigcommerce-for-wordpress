<?php


namespace BigCommerce\Import\Importers\Products;


use BigCommerce\Api\v3\Api\CatalogApi;
use BigCommerce\Api\v3\Model;
use BigCommerce\Import\Import_Strategy;

/**
 * Strategy for skipping a product during the import process.
 */
class Product_Ignorer implements Import_Strategy {
    /**
     * @var Model\Product The product data from the BigCommerce API.
     */
    private $product;

    /**
     * @var Model\Listing The channel listing data from the BigCommerce API.
     */
    private $listing;

    /**
     * @var CatalogApi The Catalog API instance used for product operations.
     */
    private $catalog;

    /**
     * @var int The WordPress post ID associated with the product.
     */
    private $post_id;

    /**
     * @var \WP_Term The term associated with the product's channel.
     */
    private $channel_term;

    /**
     * Product_Ignorer constructor.
     *
     * Initializes the product ignorer with relevant data and dependencies.
     *
     * @param Model\Product $product      The product data.
     * @param Model\Listing $listing      The channel listing data.
     * @param \WP_Term      $channel_term The WordPress term representing the channel.
     * @param CatalogApi    $catalog      The Catalog API instance.
     * @param int           $post_id      The WordPress post ID.
     */
    public function __construct( Model\Product $product, Model\Listing $listing, \WP_Term $channel_term, CatalogApi $catalog, $post_id ) {
        $this->product      = $product;
        $this->listing      = $listing;
        $this->catalog      = $catalog;
        $this->post_id      = $post_id;
        $this->channel_term = $channel_term;
    }

    /**
     * Skips the import process for the current product.
     *
     * Triggers a custom action hook for other processes to react to the product being skipped.
     *
     * @return int The WordPress post ID of the skipped product.
     */
    public function do_import() {
        /**
         * Fires when a product is skipped during the import process.
         *
         * @param int           $post_id      The WordPress post ID of the skipped product.
         * @param Model\Product $product      The product data from the BigCommerce API.
         * @param Model\Listing $listing      The channel listing data.
         * @param \WP_Term      $channel_term The term representing the product's channel.
         * @param CatalogApi    $catalog      The Catalog API instance used for product operations.
         */
        do_action( 'bigcommerce/import/product/skipped', $this->post_id, $this->product, $this->listing, $this->channel_term, $this->catalog );

        return $this->post_id;
    }
}