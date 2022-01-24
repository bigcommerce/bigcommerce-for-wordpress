<?php

namespace BigCommerce\Webhooks\Customer;

use BigCommerce\Logging\Error_Log;

/**
 * @class Customer_Deleter
 *
 * Handle customer delete webhook requests
 */
class Customer_Deleter {

	use Customer;

	/**
	 * Delete single customer by id
	 *
	 * @param $customer_id
	 *
	 * @return bool
	 */
	public function handle_request( $customer_id ) {
		try {
			$user = $this->get_by_bc_id( $customer_id );

			if ( empty( $user ) ) {
				return;
			}

			require_once( ABSPATH . 'wp-admin/includes/user.php' );
			wp_delete_user( $user->ID );
		} catch (\InvalidArgumentException $exception) {
			do_action( 'bigcommerce/log', Error_Log::INFO, __( 'Wrong customer id', 'bigcommerce' ), [
					'customer_id' => $customer_id,
			] );

			return;
		}
	}

}
