<?php

namespace BigCommerce\Webhooks\Customer;

use Bigcommerce\Api\Client;
use BigCommerce\Import\Processors\Store_Settings;
use BigCommerce\Logging\Error_Log;
use BigCommerce\Settings\Sections\Account_Settings;
use BigCommerce\Taxonomies\Channel\Channel;
use BigCommerce\Taxonomies\Channel\Connections;

/**
 * @class Customer_Channel_Updater
 *
 * Handle customer create webhook requests
 */
class Customer_Channel_Updater extends Customer_Saver {

	/**
	 * Meta key for storing customer channel metadata
	 *
	 * @var string
	 */
	const CUSTOMER_CHANNEL_META = 'bigcommerce_customer_channel_meta';

	/**
	 * Meta key for storing the customer's original channel
	 *
	 * @var string
	 */
	const CUSTOMER_ORIGIN_CHANNEL = 'bigcommerce_customer_origin_channel';

	/**
	 * Updates customer meta with customer API response
	 *
	 * @param int $customer_id
	 * @param array $channel_ids
	 *
	 * @return bool
	 */
	public function handle_request( int $customer_id = 0, array $channel_ids = [] ): bool {
		[ $matching_user, $customer_response ] = $this->get_customer_match( $customer_id );

		if ( ! $matching_user ) {
			$message = sprintf( '%s webhook: user not found', Customer_Channel_Webhook::SCOPE );
			do_action( 'bigcommerce/log', Error_Log::ERROR, __( $message, 'bigcommerce' ), [
				'customer_id' => $customer_id,
			], 'webhooks' );

			return false;
		}

		if ( $this->maybe_remove_customer_msf( $customer_response ) ) {
			return false;
		}

		$this->save_customer_channel_data( $matching_user, $customer_response );

		return true;
	}

}
