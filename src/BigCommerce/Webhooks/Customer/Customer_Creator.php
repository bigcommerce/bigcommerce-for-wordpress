<?php

namespace BigCommerce\Webhooks\Customer;

use BigCommerce\Accounts\Customer;
use BigCommerce\Accounts\Roles\Customer as Customer_Role;
use BigCommerce\Accounts\User_Profile_Settings;
use Bigcommerce\Api\Client;
use BigCommerce\Logging\Error_Log;

/**
 * @class Customer_Creator
 *
 * Handle customer create webhook requests
 */
class Customer_Creator {

	/**
	 * Create new customer if it doesn't exist
	 *
	 * @param $customer_id
	 *
	 * @return false|int|\WP_Error
	 */
	public function handle_request( $customer_id ) {
		$customer_response = Client::getResource( sprintf( '/customers/%d', $customer_id ) );

		if ( empty( $customer_response ) ) {
			do_action( 'bigcommerce/log', Error_Log::ERROR, __( 'Customer create webhook failed. Could not get customer details', 'bigcommerce' ), [
					'customer_id' => $customer_id,
			], 'webhooks' );

			return false;
		}

		$username      = $customer_response->email;
		$first_name    = $customer_response->first_name;
		$last_name     = $customer_response->last_name;
		$matching_user = get_user_by( 'email', $username );

		if ( $matching_user ) {
			$this->handle_customer_id_update( $matching_user->ID, $customer_id );

			do_action( 'bigcommerce/log', Error_Log::INFO, __( 'User exists, update customer id', 'bigcommerce' ), [
				'customer_id' => $customer_id,
				'user_id'     => $matching_user->ID,
			], 'webhooks' );

			return false;
		}

		$password = wp_generate_password();

		$user_id = wp_create_user( $username, $password, $username );

		if ( is_wp_error( $user_id ) ) {
			do_action( 'bigcommerce/log', Error_Log::ERROR, __( 'Could not create a user via webhook', 'bigcommerce' ), [
				'customer_id' => $customer_id,
				'result'      => $user_id,
			], 'webhooks' );

			return false;
		}

		$user = new \WP_User( $user_id );

		/**
		 * Filter the default role given to new users
		 *
		 * @param string $role
		 */
		$role = apply_filters( 'bigcommerce/user/default_role', Customer_Role::NAME );
		$user->set_role( $role );

		// We are not able to get password from API. We also can't update it via API. In other case customer won't able to login on BC
		// Stop User Password Sync
		update_user_meta( $user_id, User_Profile_Settings::SYNC_PASSWORD, false );
		update_user_meta( $user_id, 'first_name',  $first_name);
		update_user_meta( $user_id, 'last_name',  $last_name);

		$this->handle_customer_id_update( $user_id, $customer_id );

		return $user_id;
	}

	/**
	 * Set customer id
	 *
	 * @param $user_id
	 * @param $customer_id
	 */
	private function handle_customer_id_update( $user_id, $customer_id ) {
		$customer = new Customer( $user_id );
		$customer->set_customer_id( $customer_id );
	}
}
