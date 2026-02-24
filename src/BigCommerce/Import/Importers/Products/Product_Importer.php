<?php


namespace BigCommerce\Import\Importers\Products;


use BigCommerce\Api\v3\Api\CatalogApi;
use BigCommerce\Api\v3\Api\ChannelsApi;
use BigCommerce\Api\v3\Model\Listing;
use BigCommerce\Api\v3\Model\Product;
use BigCommerce\Import\Importers\Products\Product_Strategy_Factory;
use BigCommerce\Import\Import_Strategy;
use BigCommerce\Logging\Error_Log;

/**
 * Handles the import of a product from BigCommerce to WordPress.
 */
class Product_Importer {
    /**
     * @var Product The Product data retrieved from the BigCommerce catalog API.
     */
    private $product;

    /**
     * @var Listing The Listing data from the BigCommerce channel API.
     */
    private $listing;

    /**
     * @var CatalogApi The Catalog API instance used to interact with the BigCommerce catalog.
     */
    private $catalog_api;

    /**
     * @var \WP_Term The WordPress term corresponding to the product's channel.
     */
    private $channel_term;

    /**
     * Product_Importer constructor.
     *
     * Initializes the product importer with relevant product, listing, catalog API, and channel term data.
     *
     * @param Product    $product      The product data from the BigCommerce catalog API.
     * @param Listing    $listing      The channel listing data from BigCommerce.
     * @param CatalogApi $catalog_api  The Catalog API instance for product management.
     * @param \WP_Term   $channel_term The WordPress term representing the product's channel.
     */
    public function __construct( Product $product, Listing $listing, CatalogApi $catalog_api, \WP_Term $channel_term ) {
        $this->product      = $product;
        $this->listing      = $listing;
        $this->catalog_api  = $catalog_api;
        $this->channel_term = $channel_term;
    }

    /**
     * Initiates the import process for the product.
     *
     * Creates the appropriate strategy for importing the product and executes it.
     *
     * @return int The ID of the imported WordPress post.
     */
    public function import() {
        $strategy_factory = new Product_Strategy_Factory( $this->product, $this->listing, $this->channel_term, $this->catalog_api, Import_Strategy::VERSION );

        $strategy = $strategy_factory->get_strategy();

        return $strategy->do_import();
    }
}