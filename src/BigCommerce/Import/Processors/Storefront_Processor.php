<?php

namespace BigCommerce\Import\Processors;

use BigCommerce\Api\v3\Api\SettingsApi;
use BigCommerce\Logging\Error_Log;
use BigCommerce\Taxonomies\Channel\Channel;
use BigCommerce\Taxonomies\Channel\Connections;

/**
 * @class Storefront_Processor
 *        Process storefront settings for primary storefront
 */
class Storefront_Processor {

	const STOREFRONT_NAME    = 'bigcommerce_storefront_name';
	const STOREFRONT_ADDRESS = 'bigcommerce_storefront_address';
	const STOREFRONT_EMAIl   = 'bigcommerce_storefront_email';
	const STOREFRONT_PHONE   = 'bigcommerce_storefront_phone';

	const DOWN_FOR_MAINTENANCE = 'bigcommerce_down_for_maintenance_message';
	const PRE_LAUNCH_MESSAGE   = 'bigcommerce_prelaunch_message';
	const PRE_LAUNCH_PASSWORD  = 'bigcommerce_prelaunch_password';

	const SHOW_PRODUCT_PRICE            = 'bigcommerce_storefront_show_product_price';
	const SHOW_PRODUCT_SKU              = 'bigcommerce_storefront_show_product_sku';
	const SHOW_PRODUCT_WEIGHT           = 'bigcommerce_storefront_show_product_weight';
	const SHOW_PRODUCT_BRAND            = 'bigcommerce_storefront_show_product_brand';
	const SHOW_PRODUCT_SHIPPING         = 'bigcommerce_storefront_show_product_shipping';
	const SHOW_PRODUCT_RATING           = 'bigcommerce_storefront_show_product_rating';
	const SHOW_PRODUCT_ADD_TO_CART_LINK = 'bigcommerce_storefront_show_add_to_cart_link';
	const DEFAULT_PREORDER_MESSAGE      = 'bigcommerce_storefront_default_preorder_message';
	const SHOW_BREADCRUMBS_PRODUCT_PAGE = 'bigcommerce_storefront_show_breadcrumbs_product_pages';
	const SHOW_ADD_TO_CART_QTY_BOX      = 'bigcommerce_storefront_show_add_to_cart_qty_box';
	const SHOW_ADD_TO_WISHLIST          = 'bigcommerce_storefront_show_add_to_wishlist';
	const HIDE_PRICE_FROM_GUESTS        = 'bigcommerce_storefront_hide_price_from_guests';

	/**
	 * @var \BigCommerce\Api\v3\Api\SettingsApi
	 */
	private $api;

	/**
	 * @var \BigCommerce\Taxonomies\Channel\Connections
	 */
	private $connections;

	public function __construct( SettingsApi $api, Connections $connections ) {
		$this->api         = $api;
		$this->connections = $connections;
	}

	public function run() {
		do_action( 'bigcommerce/log', Error_Log::INFO, __( 'Retrieve storefront settings', 'bigcommerce' ), [] );

		$channels = $this->connections->active();

		if ( empty( $channels ) ) {
			do_action( 'bigcommerce/log', Error_Log::INFO, __( 'Could not retrieve channels settings', 'bigcommerce' ), [] );
			return;
		}

		foreach ( $channels as $channel ) {
			$channel_id = get_term_meta( $channel->term_id, Channel::CHANNEL_ID, true );
			$this->handle_profile_settings( $channel->term_id, $channel_id );
			$this->handle_status( $channel->term_id, $channel_id );
			$this->handle_products( $channel->term_id, $channel_id );
		}

	}

	/**
	 * Get and save storefront product settings
	 * /stores/{store_hash}/v3/settings/storefront/product
	 * https://developer.bigcommerce.com/api-reference/d4a004e640c74-get-storefront-product-settings
	 *
	 * @param int $term_id
	 * @param int $channel_id
	 *
	 * @return bool
	 */
	public function handle_products( int $term_id, int $channel_id = 0 ): bool {
		if ( empty( $channel_id ) ) {
			do_action( 'bigcommerce/log', Error_Log::INFO, __( 'Channel ID is empty. Could not get storefront product settings', 'bigcommerce' ), [] );

			return false;
		}

		do_action( 'bigcommerce/log', Error_Log::INFO, __( 'Get storefront product settings', 'bigcommerce' ), [
			'channel_id' => $channel_id,
		] );
		try {
			$response = $this->api->getStorefrontProduct( $channel_id );

			$entries  = [
				self::SHOW_PRODUCT_PRICE            => ! is_null( $response->data->show_product_price ) ? ( int ) $response->data->show_product_price : '',
				self::SHOW_PRODUCT_SKU              => ! is_null( $response->data->show_product_sku ) ? ( int ) $response->data->show_product_sku : '',
				self::SHOW_PRODUCT_WEIGHT           => ! is_null( $response->data->show_product_weight ) ? ( int ) $response->data->show_product_weight : '',
				self::SHOW_PRODUCT_BRAND            => ! is_null( $response->data->show_product_brand ) ? ( int ) $response->data->show_product_brand : '',
				self::SHOW_PRODUCT_SHIPPING         => ! is_null( $response->data->show_product_shipping ) ? ( int ) $response->data->show_product_shipping : '',
				self::SHOW_PRODUCT_RATING           => ! is_null( $response->data->show_product_rating ) ? ( int ) $response->data->show_product_rating : '',
				self::SHOW_PRODUCT_ADD_TO_CART_LINK => ! is_null( $response->data->show_add_to_cart_link ) ? ( int ) $response->data->show_add_to_cart_link : '',
				self::DEFAULT_PREORDER_MESSAGE      => $response->data->default_preorder_message,
				self::SHOW_BREADCRUMBS_PRODUCT_PAGE => $response->data->show_breadcrumbs_product_pages,
				self::SHOW_ADD_TO_CART_QTY_BOX      => ! is_null( $response->data->show_add_to_cart_qty_box ) ? ( int ) $response->data->show_add_to_cart_qty_box : '',
				self::SHOW_ADD_TO_WISHLIST          => ! is_null( $response->data->show_add_to_wishlist ) ? ( int ) $response->data->show_add_to_wishlist : '',
				self::HIDE_PRICE_FROM_GUESTS        => ! is_null( $response->data->hide_price_from_guests ) ? ( int ) $response->data->hide_price_from_guests : '',
			];
		} catch ( \Throwable $exception ) {
			do_action( 'bigcommerce/log', Error_Log::ERROR, __( 'Could not get storefront product settings', 'bigcommerce' ), [
				'channel_id' => $channel_id,
				'code'       => $exception->getCode(),
				'message'    => $exception->getMessage(),
			] );
			return false;
		}

		do_action( 'bigcommerce/log', Error_Log::INFO, __( 'Save storefront product settings', 'bigcommerce' ), [
			'channel_id' => $channel_id,
		] );

		$this->handle_options( $term_id, $entries );

		return true;
	}

	/**
	 * Get and save storefront status settings
	 * /stores/{store_hash}/v3/settings/storefront/status
	 * https://developer.bigcommerce.com/api-reference/9c3e93feb6a21-get-storefront-status
	 *
	 * @param int $term_id
	 * @param int $channel_id
	 *
	 * @return bool
	 */
	public function handle_status( int $term_id, int $channel_id = 0 ): bool {
		if ( empty( $channel_id ) ) {
			do_action( 'bigcommerce/log', Error_Log::INFO, __( 'Channel ID is empty. Could not get storefront status', 'bigcommerce' ), [
				'channel_id' => $channel_id,
			] );

			return false;
		}

		do_action( 'bigcommerce/log', Error_Log::INFO, __( 'Get storefront status settings', 'bigcommerce' ), [
			'channel_id' => $channel_id,
		] );
		try {
			$response = $this->api->getStorefrontStatus( $channel_id );
			$entries  = [
				self::DOWN_FOR_MAINTENANCE => $response->data->down_for_maintenance_message,
				self::PRE_LAUNCH_MESSAGE   => $response->data->prelaunch_message,
				self::PRE_LAUNCH_PASSWORD  => $response->data->prelaunch_password,
			];
		} catch ( \Throwable $exception ) {
			do_action( 'bigcommerce/log', Error_Log::ERROR, __( 'Could not get storefront status', 'bigcommerce' ), [
				'channel_id' => $channel_id,
				'code'       => $exception->getCode(),
				'message'    => $exception->getMessage(),
			] );
			return false;
		}

		do_action( 'bigcommerce/log', Error_Log::INFO, __( 'Save storefront status settings', 'bigcommerce' ), [
			'channel_id' => $channel_id,
		] );
		$this->handle_options( $term_id, $entries );

		return true;
	}

	/**
	 * Get and store storefront profile settings
	 * /stores/{store_hash}/v3/settings/storefront/profile
	 * https://developer.bigcommerce.com/api-reference/ac86db39bc51e-get-store-profile-settings
	 *
	 * @param int $term_id
	 * @param int $channel_id
	 *
	 * @return bool
	 */
	public function handle_profile_settings( int $term_id, int $channel_id = 0 ): bool {
		if ( empty( $channel_id ) ) {
			do_action( 'bigcommerce/log', Error_Log::INFO, __( 'Channel ID is empty. Could not get storefront profile', 'bigcommerce' ), [] );

			return false;
		}

		do_action( 'bigcommerce/log', Error_Log::INFO, __( 'Get storefront profile settings', 'bigcommerce' ), [
			'channel_id' => $channel_id,
		] );
		try {
			$response = $this->api->getStorefrontProfile( $channel_id );
			$entries  = [
				self::STOREFRONT_EMAIl   => $response->data->store_email,
				self::STOREFRONT_ADDRESS => $response->data->store_address,
				self::STOREFRONT_NAME    => $response->data->store_name,
				self::STOREFRONT_PHONE   => $response->data->store_phone,
			];
		} catch ( \Throwable $exception ) {
			do_action( 'bigcommerce/log', Error_Log::ERROR, __( 'Could not get storefront profile settings', 'bigcommerce' ), [
				'channel_id' => $channel_id,
				'code'       => $exception->getCode(),
				'message'    => $exception->getMessage(),
			] );
			return false;
		}

		do_action( 'bigcommerce/log', Error_Log::INFO, __( 'Save storefront profile settings', 'bigcommerce' ), [
			'channel_id' => $channel_id,
		] );
		$this->handle_options( $term_id, $entries );

		return true;
	}

	/**
	 * @param int   $term_id
	 * @param array $entries
	 */
	protected function handle_options( int $term_id, array $entries = [] ): void {
		if ( empty( $entries ) ) {
			return;
		}

		foreach ( $entries as $option => $value ) {
			update_term_meta( $term_id, $option, $value );
		}
	}
}
