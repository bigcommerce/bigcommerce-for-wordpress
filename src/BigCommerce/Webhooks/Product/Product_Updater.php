<?php


namespace BigCommerce\Webhooks\Product;

use BigCommerce\Api\v3\Api\CatalogApi;
use BigCommerce\Api\v3\Api\ChannelsApi;
use BigCommerce\Api\v3\ApiException;
use BigCommerce\Exceptions\Product_Not_Found_Exception;
use BigCommerce\Import\Importers\Products\Product_Importer;
use BigCommerce\Logging\Error_Log;
use BigCommerce\Post_Types\Product\Product;
use BigCommerce\Taxonomies\Channel\Channel;
use BigCommerce\Taxonomies\Channel\Connections;

/**
 * Class Product_Updater
 *
 * Handles the process of updating products in BigCommerce through webhooks.
 *
 * This class is responsible for managing the re-import and update of product data
 * when triggered by a webhook. It interacts with the BigCommerce Catalog and Channels APIs
 * to retrieve and update product details while ensuring that listing updates or deletions
 * are temporarily disabled during the process. Additionally, it ensures that updates are
 * applied to all active channels for a product.
 *
 * @package BigCommerce\Webhooks\Product
 */
class Product_Updater {

	/** @var CatalogApi */
	private $catalog;
	/** @var ChannelsApi */
	private $channels;

	/**
	 * Product_Updater constructor.
	 *
	 * Initializes the Product_Updater class with the necessary dependencies to 
	 * interact with BigCommerce Catalog and Channels APIs.
	 *
	 * @param CatalogApi  $catalog  The Catalog API instance for interacting with product data.
	 * @param ChannelsApi $channels The Channels API instance for managing channel-related operations.
	 */
	public function __construct( CatalogApi $catalog, ChannelsApi $channels ) {
		$this->catalog  = $catalog;
		$this->channels = $channels;
	}

	/**
	 * Re-import a previously imported product.
	 *
	 * This method handles updating a product based on the given BigCommerce product ID.
	 * It ensures all active channels are updated and skips the update if no channels are active.
	 * Applies temporary filters to prevent unintended listing updates or deletions during the process.
	 *
	 * @param int $product_id BigCommerce product ID to update.
	 * 
	 * @return void
	 *
	 * @action Webhook_Cron_Tasks::UPDATE_PRODUCT Triggered when the product update task is processed.
	 * @throws ApiException If the API request for product data fails.
	 */
	public function update( $product_id ) {
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
				$this->update_for_channel( $product, $channel );
			}
		} catch ( ApiException $e ) {
			do_action( 'bigcommerce/import/error', $e->getMessage(), [
				'response' => $e->getResponseBody(),
				'headers'  => $e->getResponseHeaders(),
			] );
			do_action( 'bigcommerce/log', Error_Log::DEBUG, $e->getTraceAsString(), [], 'webhooks' );
		} finally {
			// unhook the filters we added at the start
			remove_filter( 'bigcommerce/channel/listing/should_update', $empty, 10 );
			remove_filter( 'bigcommerce/channel/listing/should_delete', $empty, 10 );
		}
	}

	/**
     * Start product update for the specified channel
     *
	 * @param \BigCommerce\Api\v3\Model\Product $product
	 * @param \WP_Term                          $channel
	 *
	 * @return void
	 * @throws ApiException
	 */
	private function update_for_channel( \BigCommerce\Api\v3\Model\Product $product, \WP_Term $channel ) {
		$channel_id = get_term_meta( $channel->term_id, Channel::CHANNEL_ID, true );
		if ( empty( $channel_id ) ) {
			return;
		}
		$listing_id = $this->get_listing_id( $product->getId(), $channel );
		if ( ! $listing_id ) {
			/**
			 * Fires if product update import skipped.
			 *
			 * @param string $message       Message.
			 * @param int    $product_bc_id Product BC ID.
			 */
			do_action( 'bigcommerce/import/update_product/skipped', sprintf( __( 'No listing found for product ID %d. Aborting.', 'bigcommerce' ), $product->getId() ) );

			return;
		}

		$listing  = $this->channels->getChannelListing( $channel_id, $listing_id )->getData();
		$importer = new Product_Importer( $product, $listing, $this->catalog, $channel );
		$importer->import();
	}

	/**
	 * Find the listing ID associated with the product
	 *
	 * @param int      $product_id
	 * @param \WP_Term $channel
	 *
	 * @return int
	 */
	private function get_listing_id( $product_id, \WP_Term $channel ) {
		try {
			$product = Product::by_product_id( $product_id, $channel, ['post_status' => 'any'] );
		} catch ( Product_Not_Found_Exception $e ) {
			return 0;
		}

		$listing    = $product->get_listing_data();
		$listing_id = 0;
		if ( ! empty( $listing ) && isset( $listing->listing_id ) ) {
			$listing_id = (int) $listing->listing_id;
		}

		return $listing_id;
	}
}
