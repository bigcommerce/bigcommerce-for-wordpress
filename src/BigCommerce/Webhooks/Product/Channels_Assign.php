<?php

namespace BigCommerce\Webhooks\Product;

use BigCommerce\Api\v3\Model\Product;
use BigCommerce\Import\Importers\Products\Product_Importer;
use BigCommerce\Logging\Error_Log;
use BigCommerce\Taxonomies\Channel\Channel;

class Channels_Assign extends Channels_Manager {

	public function handle_request( $product_id, $channel_id ) {
		$channel = $this->get_channel( $channel_id );

		if ( empty( $channel ) ) {
			do_action( 'bigcommerce/log', Error_Log::INFO, __( 'Requested channel does not exist', 'bigcommerce' ), [
				'channel_id' => $channel_id,
			], 'webhooks' );

			return;
		}

		$product = $this->maybe_get_existing_product( $product_id );

		if ( ! empty( $product ) && $channel->term_id === $product->get_channel()->term_id ) {
			do_action( 'bigcommerce/log', Error_Log::INFO, __( 'Product is added to channel already. Start product update', 'bigcommerce' ), [
				'channel_id' => $channel_id,
				'product'    => $product_id,
			], 'webhooks' );

			$this->handle_product_update( $product, $channel );

			return;
		}

		/**
		 * Product does not exist in channel. Start product import process
		 */
		try {
			$product = $this->catalog_api->getProductById( $product_id, [
				'include' => [ 'variants', 'custom_fields', 'images', 'videos', 'bulk_pricing_rules', 'options', 'modifiers' ],
			] )->getData();

			$this->handle_product_creation( $product, $channel );
		} catch ( \Exception $e ) {
			do_action( 'bigcommerce/log', Error_Log::INFO, $e->getMessage(), [
				'response' => $e->getResponseBody(),
				'headers'  => $e->getResponseHeaders(),
			], 'webhooks' );
			do_action( 'bigcommerce/log', Error_Log::DEBUG, $e->getTraceAsString(), [], 'webhooks' );
		}
	}

	private function handle_product_creation( Product $product, \WP_Term $channel ) {
		$channel_id       = get_term_meta( $channel->term_id, Channel::CHANNEL_ID, true );
		$listing_response = $this->channels_api->listChannelListings( $channel_id, [
			'product_id:in' => $product->getId(),
			'limit'         => 1,
		] )->getData();

		if ( empty( $listing_response ) ) {
			do_action( 'bigcommerce/log', Error_Log::INFO, __( 'Listing not found ', 'bigcommerce' ), [
				'response'   => $listing_response,
				'product_id' => $product->getId(),
			], 'webhooks' );

			return;
		}

		$listing = reset( $listing_response );

		$importer = new Product_Importer( $product, $listing, $this->catalog_api, $channel );
		$importer->import();
	}

	private function handle_product_update( \BigCommerce\Post_Types\Product\Product $product, \WP_Term $channel ) {
		try {
			$local_listing = $product->get_listing_data();
			$listing_id    = ! empty( $local_listing ) && isset( $local_listing->listing_id ) ? $local_listing->listing_id : 0;

			if ( ! $listing_id ) {
				/**
				 * Fires if product update import skipped.
				 *
				 * @param string $message       Message.
				 * @param int    $product_bc_id Product BC ID.
				 */
				do_action( 'bigcommerce/import/update_product/skipped', sprintf( __( 'No listing found for product ID %d. Aborting.', 'bigcommerce' ), $product->bc_id() ) );

				return;
			}
			$channel_id = get_term_meta( $channel->term_id, Channel::CHANNEL_ID, true );
			$product    = $this->catalog_api->getProductById( $product->bc_id(), [
					'include' => [ 'variants', 'custom_fields', 'images', 'videos', 'bulk_pricing_rules', 'options', 'modifiers' ],
			] )->getData();
			$listing    = $this->channels_api->getChannelListing( $channel_id, $listing_id )->getData();
			$importer   = new Product_Importer( $product, $listing, $this->catalog_api, $channel );
			$importer->import();
		} catch ( \Exception $e ) {
			do_action( 'bigcommerce/log', Error_Log::INFO, $e->getMessage(), [
					'response' => $e->getResponseBody(),
					'headers'  => $e->getResponseHeaders(),
			], 'webhooks' );
			do_action( 'bigcommerce/log', Error_Log::DEBUG, $e->getTraceAsString(), [], 'webhooks' );
		}
	}

}
