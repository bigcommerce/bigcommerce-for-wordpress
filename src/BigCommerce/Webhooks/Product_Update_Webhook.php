<?php
/**
 * Product_Update_Webhook class
 *
 * @package BigCommmerce
 */

namespace BigCommerce\Webhooks;

use BigCommerce\Settings\Api_Credentials;
use BigCommerce\Rewrites\Action_Endpoint;

/**
 * Sets up the webhook that runs on product update.
 */
class Product_Update_Webhook extends Webhook {
	const SCOPE  = 'store/product/*';
	const ACTION = 'product_update';

	/**
	 * Hook callback to run when a product update webhook is received.
	 *
	 * @action bigcommerce/action_endpoint/product_update
	 */
	public function receive() {
		$request = $this->get_webhook_payload();

		$validates = $this->validate( $request );

		if ( is_wp_error( $validates ) ) {
			wp_send_json_error( $validates );
		}

		/**
		 * Fires when a product has been updated in the BigCommerce store.
		 *
		 * @param int $product_id BigCommerce product ID.
		 */
		do_action( 'bigcommerce/webhooks/product_updated', intval( $request['data']['id'] ) );

		wp_send_json_success();
	}
}
