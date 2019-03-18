<?php


namespace BigCommerce\Webhooks;

use BigCommerce\Api\v3\Api\CatalogApi;
use BigCommerce\Api\v3\Api\ChannelsApi;
use BigCommerce\Api\v3\ApiException;
use BigCommerce\Import\Importers\Products\Product_Importer;
use BigCommerce\Post_Types\Product\Product;
use BigCommerce\Settings\Sections\Channels;

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
		$channel_id = get_option( Channels::CHANNEL_ID, 0 );
		if ( empty( $channel_id ) ) {
			do_action( 'bigcommerce/import/error', __( 'Channel ID is not set. Product import canceled.', 'bigcommerce' ) );

			return;
		}

		$listing_id = $this->get_listing_id( $product_id );
		if ( ! $listing_id ) {
			do_action( 'bigcommerce/import/update_product/skipped', sprintf( __( 'No listing found for product ID %d. Aborting.', 'bigcommerce' ), $product_id ) );

			return;
		}

		try {
			$listing  = $this->channels->getChannelListing( $channel_id, $listing_id );
			$product  = $this->catalog->getProductById( $product_id, [
				'include' => [ 'variants', 'custom_fields', 'images', 'bulk_pricing_rules' ],
			] );
			$importer = new Product_Importer( $product->getData(), $listing->getData(), $this->catalog, $this->channels, $channel_id );
			$importer->import();
		} catch ( ApiException $e ) {
			do_action( 'bigcommerce/import/error', $e->getMessage(), [
				'response' => $e->getResponseBody(),
				'headers'  => $e->getResponseHeaders(),
			] );

			return;
		}
	}

	/**
	 * Find the listing ID associated with the product
	 *
	 * @param int $product_id
	 *
	 * @return int
	 */
	private function get_listing_id( $product_id ) {
		$product    = Product::by_product_id( $product_id );
		$listing    = $product->get_listing_data();
		$listing_id = 0;
		if ( ! empty( $listing ) && isset( $listing->listing_id ) ) {
			$listing_id = (int) $listing->listing_id;
		}

		return $listing_id;
	}
}