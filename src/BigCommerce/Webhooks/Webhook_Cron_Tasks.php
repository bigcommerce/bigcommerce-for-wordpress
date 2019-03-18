<?php


namespace BigCommerce\Webhooks;

/**
 * Class Webhook_Cron_Tasks
 *
 * @package BigCommerce\Webhooks
 */
class Webhook_Cron_Tasks {

	const UPDATE_PRODUCT = 'bigcommerce/wekhooks/cron/update_product';


	/**
	 * @param int $product_id
	 */
	public function set_product_update_cron_task( $params ) {
		if ( ! wp_next_scheduled( self::UPDATE_PRODUCT, $params ) ) {
			wp_schedule_single_event( time(), self::UPDATE_PRODUCT, $params );
		}
	}


}