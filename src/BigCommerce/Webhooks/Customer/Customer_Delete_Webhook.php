<?php
/**
 * Customer_Delete_Webhook class
 *
 * @package BigCommmerce
 */

namespace BigCommerce\Webhooks\Customer;

use BigCommerce\Accounts\Customer;
use BigCommerce\Logging\Error_Log;
use BigCommerce\Webhooks\Webhook;

/**
 * Class Product_Delete_Webhook
 *
 * Sets up the webhook that runs on customer delete.
 */
class Customer_Delete_Webhook extends Webhook {

	const SCOPE = 'store/customer/deleted';
	const NAME  = 'customer_delete';

	/**
	 * Fires when a customer has been deleted in the BigCommerce store.
	 *
	 * @param array $request
	 */
	public function trigger_action( $request ): void {
		/**
		 * Fires when a customer has been deleted in the BigCommerce store.
		 *
		 * @param int $customer_id BigCommerce customer ID.
		 */
		do_action( 'bigcommerce/log', Error_Log::INFO, __( 'Trigger customer delete webhook', 'bigcommerce' ), [
				'bc_id' => $request['data']['id'],
		], 'webhooks' );
		do_action( 'bigcommerce/webhooks/customer_deleted', intval( $request['data']['id'] ) );
	}

}
