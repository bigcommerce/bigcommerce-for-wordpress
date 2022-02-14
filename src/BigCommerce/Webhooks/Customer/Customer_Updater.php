<?php

namespace BigCommerce\Webhooks\Customer;

use Bigcommerce\Api\Client;
use BigCommerce\Logging\Error_Log;

/**
 * @class Customer_Updater
 *
 * Handle customer create webhook requests
 */
class Customer_Updater {

	/**
	 * Updates customer meta with customer API response
	 *
	 * @param $customer_id
	 *
	 * @return bool
	 */
	public function handle_request( $customer_id ) {
		$customer_response = Client::getResource( sprintf( '/customers/%d', $customer_id ) );

		if ( empty( $customer_response ) ) {
			do_action( 'bigcommerce/log', Error_Log::ERROR, __( 'Customer Update Webhook failed. Could not get customer details ', 'bigcommerce' ), [
					'customer_id' => $customer_id,
			], 'webhooks' );

			return false;
		}

		$username      = $customer_response->email;
		$matching_user = get_user_by( 'email', $username );

		if ( ! $matching_user ) {
			do_action( 'bigcommerce/log', Error_Log::ERROR, __( 'User not found', 'bigcommerce' ), [
					'customer_id' => $customer_id,
			], 'webhooks' );

			return false;
		}
		$update_options = [
			'first_name' => $customer_response->first_name,
			'last_name'  => $customer_response->last_name,
			'company'    => $customer_response->company,
			'phone'      => $customer_response->phone,
		];

		foreach ( $update_options as $option_name => $option_value ) {
			update_user_meta( $matching_user->ID, $option_name, $option_value );
		}

		return true;
	}

}
