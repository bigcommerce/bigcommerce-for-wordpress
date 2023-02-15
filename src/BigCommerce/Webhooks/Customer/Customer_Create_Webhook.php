<?php
/**
 * Customer_Create_Webhook class
 *
 * @package BigCommmerce
 */

namespace BigCommerce\Webhooks\Customer;

use BigCommerce\Logging\Error_Log;
use BigCommerce\Webhooks\Webhook;

/**
 * Class Customer_Create_Webhook
 *
 * Sets up the webhook that runs on customer creation.
 */
class Customer_Create_Webhook extends Webhook {

	const SCOPE = 'store/customer/created';
	const NAME  = 'customer_create';

	/**
	 * Fires when a customer has been created in the BigCommerce store.
	 *
	 * @param array $request
	 */
	public function trigger_action( $request ) {
		/**
		 * Fires when a customer has been created in the BigCommerce store.
		 *
		 * @param int $customer_id BigCommerce customer ID.
		 */
		do_action( 'bigcommerce/log', Error_Log::INFO, __( 'Trigger customer create webhook', 'bigcommerce' ), [
			'bc_id' => $request['data']['id'],
		], 'webhooks' );
		do_action( 'bigcommerce/webhooks/customer_created', intval( $request['data']['id'] ) );
	}
}
