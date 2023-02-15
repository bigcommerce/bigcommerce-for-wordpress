<?php

namespace BigCommerce\Webhooks\Customer;

use BigCommerce\Import\Processors\Store_Settings;
use BigCommerce\Logging\Error_Log;
use BigCommerce\Settings\Sections\Account_Settings;
use BigCommerce\Taxonomies\Channel\Channel;
use BigCommerce\Taxonomies\Channel\Connections;

/**
 * @class Customer_Updater
 *
 * Handle customer create webhook requests
 */
class Customer_Updater extends Customer_Saver {

	/**
	 * Updates customer meta with customer API response
	 *
	 * @param int   $customer_id
	 * @param array $channel_ids
	 *
	 * @return bool
	 */
	public function handle_request( int $customer_id = 0, array $channel_ids = [] ): bool {
		[ $matching_user, $customer_response ] = $this->get_customer_match( $customer_id );

		if ( ! $matching_user ) {
			do_action( 'bigcommerce/log', Error_Log::ERROR, __( 'User not found', 'bigcommerce' ), [
				'customer_id' => $customer_id,
			], 'webhooks' );

			return false;
		}

		// Remove customer if he was un-assigned from channel on MSF store and global logins is off
		if ( $this->maybe_remove_customer_msf( $customer_response ) ) {
			return false;
		}

		$update_options = [
			'first_name' => $customer_response->getFirstName(),
			'last_name'  => $customer_response->getLastName(),
			'company'    => $customer_response->getCompany(),
			'phone'      => $customer_response->getPhone(),
		];

		foreach ( $update_options as $option_name => $option_value ) {
			update_user_meta( $matching_user->ID, $option_name, $option_value );
		}

		$this->save_customer_channel_data( $matching_user, $customer_response );

		return true;
	}

}
