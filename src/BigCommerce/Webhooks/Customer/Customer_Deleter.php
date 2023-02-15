<?php

namespace BigCommerce\Webhooks\Customer;

use BigCommerce\Logging\Error_Log;

/**
 * @class Customer_Deleter
 *
 * Handle customer delete webhook requests
 */
class Customer_Deleter extends Customer_Saver {

	/**
	 * Delete single customer by id
	 *
	 * @param int   $customer_id
	 * @param array $channel_ids
	 *
	 * @return bool
	 */
	public function handle_request( int $customer_id = 0, array $channel_ids = [] ): bool {
		return $this->delete_customer( $customer_id );
	}

}
