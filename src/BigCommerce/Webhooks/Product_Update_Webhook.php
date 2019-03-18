<?php
/**
 * Product_Update_Webhook class
 *
 * @package BigCommmerce
 */

namespace BigCommerce\Webhooks;

/**
 * Sets up the webhook that runs on product update.
 */
class Product_Update_Webhook extends Webhook {
	const SCOPE  = 'store/product/*';
	const NAME   = 'product_update';

	public function trigger_action( $request ){
		/**
		 * Fires when a product has been updated in the BigCommerce store.
		 *
		 * @param int $product_id BigCommerce product ID.
		 */
		do_action( 'bigcommerce/webhooks/product_updated', intval( $request['data']['id'] ) );
	}
}
