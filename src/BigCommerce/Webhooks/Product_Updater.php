<?php


namespace BigCommerce\Webhooks;

use BigCommerce\Api\v3\Api\CatalogApi;
use BigCommerce\Api\v3\Api\ChannelsApi;
use BigCommerce\Api\v3\ApiException;
use BigCommerce\Import\Importers\Products\Product_Importer;
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
		$channels = $connections->active();
		if ( empty( $channels ) ) {
			do_action( 'bigcommerce/import/error', __( 'No channels connected. Product import canceled.', 'bigcommerce' ) );

			return;
		}

		foreach ( $channels as $channel ) {
			$this->update_for_channel( $product_id, $channel );
		}
	}

	private function update_for_channel( $product_id, \WP_Term $channel ) {
		$channel_id = get_term_meta( $channel->term_id, Channel::CHANNEL_ID, true );
		if ( empty( $channel_id ) ) {
			return;
		}
		$listing_id = $this->get_listing_id( $product_id, $channel );
		if ( ! $listing_id ) {
			do_action( 'bigcommerce/import/update_product/skipped', sprintf( __( 'No listing found for product ID %d. Aborting.', 'bigcommerce' ), $product_id ) );

			return;
		}

		try {
			$listing  = $this->channels->getChannelListing( $channel_id, $listing_id );
			$product  = $this->catalog->getProductById( $product_id, [
				'include' => [ 'variants', 'custom_fields', 'images', 'bulk_pricing_rules', 'options', 'modifiers' ],
			] );
			$importer = new Product_Importer( $product->getData(), $listing->getData(), $this->catalog, $channel );
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
	 * @param int      $product_id
	 * @param \WP_Term $channel
	 *
	 * @return int
	 */
	private function get_listing_id( $product_id, \WP_Term $channel ) {
		$product    = Product::by_product_id( $product_id, $channel );
		$listing    = $product->get_listing_data();
		$listing_id = 0;
		if ( ! empty( $listing ) && isset( $listing->listing_id ) ) {
			$listing_id = (int) $listing->listing_id;
		}

		return $listing_id;
	}
}