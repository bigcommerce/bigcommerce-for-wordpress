<?php


namespace BigCommerce\Webhooks;

use BigCommerce\Api\v3\Api\CatalogApi;
use BigCommerce\Api\v3\Api\ChannelsApi;
use BigCommerce\Api\v3\ApiException;
use BigCommerce\Exceptions\Product_Not_Found_Exception;
use BigCommerce\Import\Importers\Products\Product_Importer;
use BigCommerce\Logging\Error_Log;
use BigCommerce\Post_Types\Product\Product;
use BigCommerce\Taxonomies\Channel\Channel;
use BigCommerce\Taxonomies\Channel\Connections;

class Product_Updater {
	/** @var CatalogApi */
	private $catalog;
	/** @var ChannelsApi */
	private $channels;

	public function __construct( CatalogApi $catalog, ChannelsApi $channels ) {
		$this->catalog  = $catalog;
		$this->channels = $channels;
	}

	/**
	 * Re-import a previously imported product.
	 *
	 * @param int $product_id
	 *
	 * @return void
	 * @action Webhook_Cron_Tasks::UPDATE_PRODUCT
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
			do_action( 'bigcommerce/log', Error_Log::DEBUG, $e->getTraceAsString(), [] );
		} finally {
			// unhook the filters we added at the start
			remove_filter( 'bigcommerce/channel/listing/should_update', $empty, 10 );
			remove_filter( 'bigcommerce/channel/listing/should_delete', $empty, 10 );
		}
	}

	/**
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
