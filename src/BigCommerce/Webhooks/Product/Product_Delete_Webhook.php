<?php
/**
 * Product_Delete_Webhook class
 *
 * @package BigCommmerce
 */

namespace BigCommerce\Webhooks\Product;

use BigCommerce\Import\Importers\Products\Product_Remover;
use BigCommerce\Logging\Error_Log;
use BigCommerce\Taxonomies\Channel\Connections;
use BigCommerce\Webhooks\Webhook;

/**
 * Class Product_Delete_Webhook
 *
 * Sets up the webhook that runs on product update.
 */
class Product_Delete_Webhook extends Webhook {
	const SCOPE  = 'store/product/deleted';
	const NAME   = 'product_delete';

    /**
     * Fires when a product has been deleted in the BigCommerce store.
     *
     * @param array $request
     */
	public function trigger_action( $request ): void{
		/**
		 * Fires when a product has been deleted in the BigCommerce store.
		 *
		 * @param int $product_id BigCommerce product ID.
		 */
		do_action( 'bigcommerce/log', Error_Log::INFO, __( 'Trigger product delete webhook', 'bigcommerce' ), [
				'bc_id' => $request['data']['id'],
		], 'webhooks' );
		do_action( 'bigcommerce/webhooks/product_deleted', intval( $request['data']['id'] ) );
	}

    /**
     * Delete the product by id
     *
     * @param $product_id
     */
    public function delete_the_product ( $product_id ): void {
        if ( empty( $product_id ) ) {
            return;
        }

        $remover     = new Product_Remover();
        $connections = new Connections();
        $channels    = $connections->active();

        foreach ($channels as $channel) {
            $remover->remove_by_product_id( $product_id, $channel );
        }
    }
}
