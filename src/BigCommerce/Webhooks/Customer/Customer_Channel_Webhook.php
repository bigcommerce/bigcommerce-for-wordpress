<?php
/**
 * Customer_Channel_Webhook class
 *
 * @package BigCommmerce
 */

namespace BigCommerce\Webhooks\Customer;

use BigCommerce\Accounts\Customer;
use BigCommerce\Logging\Error_Log;
use BigCommerce\Webhooks\Webhook;

/**
 * Class Customer_Channel_Webhook
 *
 * Sets up the webhook that runs on customer update.
 *
 * @package BigCommerce\Webhooks
 */
class Customer_Channel_Webhook extends Webhook {

	const SCOPE = 'store/customer/channel/login/access/updated';
	const NAME  = 'customer_channel_access_update';
	const HOOK  = 'bigcommerce/webhooks/customer_channel_updated';

	/**
	 * Fires when a customer channel access has been updated in the BigCommerce store.
	 *
	 * @param array $request
	 */
	public function trigger_action( $request ) {
		/**
		 * @param int $customer_id BigCommerce customer ID.
		 * @param array channel_ids BigCommerce channels customer assigned.
		 */
		do_action( 'bigcommerce/log', Error_Log::INFO, __( 'Trigger customer channel access update webhook', 'bigcommerce' ), [
			'customer_id' => $request['data']['customer_id'],
			'channel_ids' => $request['data']['channel_ids'],
		], 'webhooks' );

		do_action( self::HOOK, intval( $request['data']['customer_id'] ), $request['data']['channel_ids'] );
	}

}
