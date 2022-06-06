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

	const SCOPE = 'store/channel/*';
	const NAME  = 'bigcommerce_channels';

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

		if ( stripos( 'category/product', $scope ) !== false ) {
			do_action( sprintf( 'bigcommerce/webhooks/product_category_channel_%s', $action ), intval( $request['data']['product_id'] ), $channel_id );
		}

		do_action( sprintf( 'bigcommerce/webhooks/product_channel_%s', $action ), intval( $request['data']['product_id'] ), $channel_id );
	}

}
