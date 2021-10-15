<?php
/**
 * Product_Update_Webhook class
 *
 * @package BigCommmerce
 */

namespace BigCommerce\Webhooks;

/**
 * Class Product_Inventory_Update_Webhook
 *
 * Sets up the webhook that runs on product update.
 *
 * @package BigCommerce\Webhooks
 */
class Product_Inventory_Update_Webhook extends Webhook {
	const SCOPE  = 'store/product/inventory/*';
	const NAME   = 'inventory_update';

	/**
     * Fires when a product inventory webhooks has been received from the BigCommerce store.
     *
	 * @param array $request
	 *
	 * @return void
	 */
	public function trigger_action( $request ) {
		$product_id = intval($request[ 'data' ][ 'id' ]);
		/**
		 * Fires when a product inventory webhooks has been received from the BigCommerce store.
		 *
		 * @param int $product_id BigCommerce product ID.
		 */
		do_action( 'bigcommerce/webhooks/product_inventory_updated',   ['product_id' => $product_id]  );
	}
}
