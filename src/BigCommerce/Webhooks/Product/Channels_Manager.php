<?php

namespace BigCommerce\Webhooks\Product;

use BigCommerce\Api\v3\Api\CatalogApi;
use BigCommerce\Api\v3\Api\ChannelsApi;
use BigCommerce\Logging\Error_Log;
use BigCommerce\Post_Types\Product\Product;
use BigCommerce\Taxonomies\Channel\Channel;

class Channels_Manager {

	/**
	 * @var \BigCommerce\Api\v3\Api\CatalogApi
	 */
	protected $catalog_api;
	/**
	 * @var \BigCommerce\Api\v3\Api\ChannelsApi
	 */
	protected $channels_api;

	public function __construct( CatalogApi $catalog_api, ChannelsApi $channels_api ) {
		$this->catalog_api  = $catalog_api;
		$this->channels_api = $channels_api;
	}

	protected function get_channel( $channel_id ) {
		$channels = get_terms( [
			'taxonomy'   => Channel::NAME,
			'meta_key'   => Channel::CHANNEL_ID,
			'meta_value' => $channel_id,
			'meta_query' => [
				[
					'key'     => Channel::STATUS,
					'value'   => [ Channel::STATUS_PRIMARY, Channel::STATUS_CONNECTED ],
					'compare' => 'IN',
				],
			],
		] );

		if ( empty( $channels ) || is_wp_error( $channels ) ) {
			do_action( 'bigcommerce/log', Error_Log::INFO, __( 'Could not find the channel', 'bigcommerce' ), [
				'channel_id' => $channel_id,
			], 'webhooks' );

			return false;
		}

		return reset( $channels );
	}

	protected function maybe_get_existing_product( $product_id ) {
		global $wpdb;
		$query   = $wpdb->prepare( "SELECT post_id FROM {$wpdb->postmeta} WHERE meta_key = %s AND meta_value = %d", Product::BIGCOMMERCE_ID, $product_id );
		$post_id = $wpdb->get_var( $query );

		if ( empty( $post_id ) || is_wp_error( $post_id ) ) {
			return false;
		}

		return new Product( $post_id );
	}

}
