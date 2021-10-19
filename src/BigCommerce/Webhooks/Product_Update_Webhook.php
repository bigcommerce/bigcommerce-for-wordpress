<?php
/**
 * Product_Update_Webhook class
 *
 * @package BigCommmerce
 */

namespace BigCommerce\Webhooks;

/**
 * Class Product_Update_Webhook
 *
 * Sets up the webhook that runs on product update.
 *
 * @package BigCommerce\Webhooks
 */
class Product_Update_Webhook extends Webhook {
	const SCOPE  = 'store/product/updated';
	const NAME   = 'product_update';

    /**
     * Fires when a product has been updated in the BigCommerce store.
     *
     * @param array $request
     */
	public function trigger_action( $request ){
		/**
		 * Fires when a product has been updated in the BigCommerce store.
		 *
		 * @param int $product_id BigCommerce product ID.
		 */
		do_action( 'bigcommerce/webhooks/product_updated', ['product_id' => intval( $request['data']['id'] )] );
	}
}
