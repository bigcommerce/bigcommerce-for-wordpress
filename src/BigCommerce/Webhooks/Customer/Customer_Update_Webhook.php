<?php
/**
 * Customer_Update_Webhook class
 *
 * @package BigCommmerce
 */

namespace BigCommerce\Webhooks\Customer;

use BigCommerce\Accounts\Customer;
use BigCommerce\Logging\Error_Log;
use BigCommerce\Webhooks\Webhook;

/**
 * Class Customer_Update_Webhook
 *
 * Sets up the webhook that runs on customer update.
 *
 * @package BigCommerce\Webhooks
 */
class Customer_Update_Webhook extends Webhook {

	const SCOPE = 'store/customer/updated';
	const NAME  = 'customer_update';

	/**
	 * Fires when a product has been updated in the BigCommerce store.
	 *
	 * @param array $request
	 */
	public function trigger_action( $request ) {
		/**
		 * Fires when a product has been updated in the BigCommerce store.
		 *
		 * @param int $customer_id BigCommerce customer ID.
		 */
		do_action( 'bigcommerce/log', Error_Log::INFO, __( 'Trigger customer update webhook', 'bigcommerce' ), [
				'bc_id' => $request['data']['id'],
		], 'webhooks' );
		do_action( 'bigcommerce/webhooks/customer_updated', intval( $request['data']['id'] ) );
	}

}
