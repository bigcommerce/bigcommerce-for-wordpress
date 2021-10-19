<?php

namespace BigCommerce\Webhooks;

use BigCommerce\Api\v3\Api\CatalogApi;
use BigCommerce\Api\v3\Api\ChannelsApi;
use BigCommerce\Api\v3\ApiException;
use BigCommerce\Api\v3\Model\Listing;
use BigCommerce\Api\v3\Model\ListingCollectionResponse;
use BigCommerce\Api\v3\Model\ListingVariant;
use BigCommerce\Api\v3\Model\Variant;
use BigCommerce\Import\Importers\Products\Product_Importer;
use BigCommerce\Logging\Error_Log;
use BigCommerce\Taxonomies\Channel\Channel;
use BigCommerce\Taxonomies\Channel\Connections;

/**
 * Class Product_Creator
 *
 * Import new single product from Bigcommerce
 */
class Product_Creator
{
    /** @var CatalogApi */
    private $catalog;

    /** @var ChannelsApi */
    private $channels;

    public function __construct( CatalogApi $catalog, ChannelsApi $channels ) {
        $this->catalog  = $catalog;
        $this->channels = $channels;
    }

    /**
     * Handle product creation logic
     *
     * @param $product_id
     */
    public function create( $product_id ) {
        $connections = new Connections();
        $channels    = $connections->active();

        if ( empty( $channels ) ) {
            do_action( 'bigcommerce/import/error', __( 'No channels connected. Product import canceled.', 'bigcommerce' ) );

            return;
        }

        try {
            /*
			 * Listings should not be updated when saving a product on import.
			 *
			 * Create our own callback instead of __return_false() so that
			 * we don't inadvertently unhook someone else's filter later
			 */
            $empty = function () {
                return false;
            };
            add_filter( 'bigcommerce/channel/listing/should_update', $empty, 10, 0 );
            add_filter( 'bigcommerce/channel/listing/should_delete', $empty, 10, 0 );

            $product = $this->catalog->getProductById( $product_id, [
                'include' => [ 'variants', 'custom_fields', 'images', 'videos', 'bulk_pricing_rules', 'options', 'modifiers' ],
            ] )->getData();

            foreach ( $channels as $channel ) {
               $this->handle_product_creation( $product, $channel );
            }
        } catch ( ApiException $e ) {
            do_action( 'bigcommerce/import/error', $e->getMessage(), [
                'response' => $e->getResponseBody(),
                'headers'  => $e->getResponseHeaders(),
            ] );
            do_action( 'bigcommerce/log', Error_Log::DEBUG, $e->getTraceAsString(), [] );
        } finally {
            // unhook the filters we added at the start
            remove_filter( 'bigcommerce/channel/listing/should_update', $empty, 10 );
            remove_filter( 'bigcommerce/channel/listing/should_delete', $empty, 10 );
        }
    }

    /**
     * Check if channel exists, adds listings to product and start product import
     *
     * @param $product
     *
     * @param \WP_Term $channel
     */
    private function handle_product_creation( $product, \WP_Term $channel )
    {
        $channel_id = get_term_meta($channel->term_id, Channel::CHANNEL_ID, true);

        if (empty($channel_id)) {
            return;
        }

        try {
            $response = $this->create_new_product_listings( $product, $channel_id );
            foreach ( $response->getData() as $listing ) {
                $this->do_import( $product, $listing, $channel );
            }
        } catch ( ApiException $e ) {
            do_action( 'bigcommerce/import/error', $e->getMessage(), [
                'response' => $e->getResponseBody(),
                'headers'  => $e->getResponseHeaders(),
            ] );
            do_action( 'bigcommerce/log', Error_Log::DEBUG, $e->getTraceAsString(), [] );

            return;
        }
    }

    /**
     * Run product import
     *
     * @param $product
     * @param $listing
     *
     * @param $channel
     */
    private function do_import( $product, $listing, $channel ) {
        $importer = new Product_Importer( $product, $listing, $this->catalog, $channel );
        $importer->import();
    }

    /**
     * Create new Listing for the product. By default, listing doesn't exist on the product
     *
     * @param $product
     * @param $channel_id
     *
     * @return ListingCollectionResponse
     *
     * @throws ApiException
     */
    private function create_new_product_listings( $product, $channel_id )
    {
        $listing_requests = [
            new Listing( [
                'channel_id' => (int) $channel_id,
                'product_id' => (int) $product->getId(),
                'state'      => $product->getIsVisible() ? 'active' : 'disabled',
                'variants'   => array_map( function ( Variant $variant ) use ( $product ) {
                    return new ListingVariant( [
                        'product_id' => (int) $product->getId(),
                        'variant_id' => (int) $variant->getId(),
                        'state'      => $variant->getPurchasingDisabled() ? 'disabled' : 'active',
                    ] );
                }, $product->getVariants() ),
            ] )
        ];

        return $this->channels->createChannelListings( $channel_id, $listing_requests );
    }
}
