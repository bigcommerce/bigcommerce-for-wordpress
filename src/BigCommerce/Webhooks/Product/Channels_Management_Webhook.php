<?php
/**
 * Product_Create_Webhook class
 *
 * @package BigCommmerce
 */

namespace BigCommerce\Webhooks\Product;

use BigCommerce\Logging\Error_Log;
use BigCommerce\Webhooks\Webhook;

/**
 * Class Channels_Management_Webhook
 *
 * Sets up the webhook that runs on channels changes.
 */
class Channels_Management_Webhook extends Webhook {

	const SCOPE                 = 'store/channel/*';
	const CHANNEL_UPDATED_SCOPE = 'store/channel/updated';
	const CHANNEL_UPDATED_HOOK  = 'bigcommerce/webhooks/channel_updated';
	const NAME                  = 'bigcommerce_channels';

	const PRODUCT_CATEGORY_CHANNEL_HOOK = 'bigcommerce/webhooks/product_category_channel';
	const PRODUCT_CHANNEL_HOOK          = 'bigcommerce/webhooks/product_channel';
	const CHANNEL_CURRENCY_UPDATE_HOOK  = 'bigcommerce/webhooks/channel_default_currency_updated';

	/**
	 * Fires when a change is applied to channels
	 *
	 * @param array $request
	 */
	public function trigger_action( $request ) {
		do_action( 'bigcommerce/log', Error_Log::INFO, __( 'Trigger channels webhook', 'bigcommerce' ), [
			'request' => $request,
		], 'webhooks' );

		/**
		 * The webhook request doesn't have channel id inside or action as a separate param. In order to get it we parse webhook scopes
		 */
		$scope      = str_replace( 'store/channel/', '', $request['scope'] );
		$channel_id = stristr( $scope, '/', true );

		if ( empty( $channel_id ) || ! is_numeric( $channel_id ) ) {
			// Channel update/create webhook has channel id in data array and does not have it in scopes
			if ( $request['scope'] === self::CHANNEL_UPDATED_SCOPE ) {
				do_action( self::CHANNEL_UPDATED_HOOK, intval( $request['data']['id'] ) );

				do_action( 'bigcommerce/log', Error_Log::INFO, __( 'Incoming channel update', 'bigcommerce' ), [
					'request'    => $request,
					'channel_id' => $request['data']['id'],
				], 'webhooks' );

				return;
			}


			do_action( 'bigcommerce/log', Error_Log::ERROR, __( 'Webhook request does not have correct channel id', 'bigcommerce' ), [
				'request'    => $request,
				'channel_id' => $channel_id,
			], 'webhooks' );

			return;
		}

		$action = trim( strrchr( $scope, '/' ), '/' );

		if ( empty( $action ) ) {
			do_action( 'bigcommerce/log', Error_Log::ERROR, __( 'Webhook request does not have correct action', 'bigcommerce' ), [
				'request'    => $request,
				'channel_id' => $channel_id,
				'action'     => $action,
			], 'webhooks' );

			return;
		}

		$this->handle_channels_webhooks_filters( $request, $scope, ( int ) $channel_id, $action );
	}

	public function handle_channels_webhooks_filters( array $request, string $scope, int $channel_id, string $action ): void {
		if ( stripos( 'settings/currency/updated', $scope ) !== false ) {
			do_action( self::CHANNEL_CURRENCY_UPDATE_HOOK, $channel_id );

			return;
		}

		if ( stripos( 'category/product', $scope ) !== false ) {
			do_action( sprintf( '%s_%s', self::PRODUCT_CATEGORY_CHANNEL_HOOK, $action ), intval( $request['data']['product_id'] ), $channel_id );

			return;
		}

		do_action( sprintf( '%s_%s', self::PRODUCT_CHANNEL_HOOK, $action ), intval( $request['data']['product_id'] ), $channel_id );
	}

}
