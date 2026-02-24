<?php

namespace BigCommerce\Webhooks\Customer;

use BigCommerce\Accounts\Customer as Account;
use BigCommerce\Api\v3\Api\CustomersApi;
use BigCommerce\Api\v3\Model\Customer;
use BigCommerce\Import\Processors\Store_Settings;
use BigCommerce\Logging\Error_Log;
use BigCommerce\Settings\Sections\Account_Settings;
use BigCommerce\Taxonomies\Channel\Channel;
use BigCommerce\Taxonomies\Channel\Connections;

/**
 * Abstract class for handling BigCommerce customer webhook operations.
 * 
 * Provides base functionality for saving, updating, and managing customer data
 * between BigCommerce and WordPress, including channel-aware operations and
 * customer data synchronization.
 *
 * @since [VERSION]
 * @package BigCommerce\Webhooks\Customer
 * 
 * @property CustomersApi $customers_api BigCommerce Customers API client instance
 */
abstract class Customer_Saver {

	/**
	 * @var CustomersApi
	 */
	public $customers_api;

	public function __construct( CustomersApi $customers_api ) {
		$this->customers_api = $customers_api;
	}

	/**
	 * Get customer details via v3 API. v3 will return channels_ids that will be used later in channel aware logic
	 *
	 * @param int $customer_id
	 *
	 * @return false|mixed
	 */
	protected function get_v3_customer_by_id( int $customer_id = 0 ) {
		try {
			$customer = $this->customers_api->customersGet( [
					'id:in' => $customer_id,
			] )->getData();

			return reset( $customer );
		} catch ( \Throwable $exception ) {
			do_action( 'bigcommerce/log', Error_Log::ERROR, __( 'Customer webhook failed. Could not get customer details', 'bigcommerce' ), [
				'customer_id' => $customer_id,
				'code'        => $exception->getCode(),
				'message'     => $exception->getMessage(),
				'trace'       => $exception->getTraceAsString(),
			], 'webhooks' );

			return false;
		}
	}

	/**
	 * Handle webhook requests
	 *
	 * @param int   $customer_id
	 * @param array $channel_ids
	 *
	 * @return bool
	 */
	abstract public function handle_request( int $customer_id = 0, array $channel_ids = [] ): bool;

	/**
	 * Get customer by BC id
	 *
	 * @param $id
	 *
	 * @return false|mixed|null
	 */
	public function get_by_bc_id( $id ) {
		if ( empty( $id ) ) {
			throw new \InvalidArgumentException( __( 'Customer ID must be a positive integer', 'bigcommerce' ) );
		}

		global $wpdb;

		$users = get_users([
			'meta_key'   => $wpdb->get_blog_prefix() . Account::CUSTOMER_ID_META,
			'meta_value' => absint( $id ),
		]);

		if ( empty( $users ) ) {
			return null;
		}

		return reset( $users );
	}

	/**
	 * @param int $customer_id
	 *
	 * @return array
	 */
	public function get_customer_match( int $customer_id ): array {
		$customer_response = $this->get_v3_customer_by_id( $customer_id );

		if ( empty( $customer_response ) ) {
			return [ false, false ];
		}

		$username = $customer_response->getEmail();

		return [
			get_user_by( 'email', $username ),
			$customer_response
		];
	}

	protected function delete_customer( int $customer_id = 0 ): bool {
		try {
			$user = $this->get_by_bc_id( $customer_id );

			if ( empty( $user ) ) {
				do_action( 'bigcommerce/log', Error_Log::INFO, __( 'Requested user does not exist. Exit', 'bigcommerce' ), [
						'customer_id' => $customer_id,
				], 'webhooks' );
				return false;
			}

			require_once( ABSPATH . 'wp-admin/includes/user.php' );
			wp_delete_user( $user->ID );

			return true;
		} catch (\InvalidArgumentException $exception) {
			do_action( 'bigcommerce/log', Error_Log::ERROR, __( 'Could not delete the user. Wrong customer id', 'bigcommerce' ), [
					'customer_id' => $customer_id,
			], 'webhooks' );

			return false;
		}
	}

	/**
	 * @param \BigCommerce\Api\v3\Model\Customer $customer
	 *
	 * @return bool
	 */
	protected function maybe_remove_customer_msf( Customer $customer ): bool {
		$is_global_login_allowed = ( bool ) get_option( Account_Settings::ALLOW_GLOBAL_LOGINS, true ) === true;
		$is_msf_on               = Store_Settings::is_msf_on();

		if ( $is_global_login_allowed || $is_msf_on || empty( ( array ) $customer->getChannelIds() ) ) {
			return false;
		}

		$connections = new Connections();
		$channel     = $connections->current();
		$channel_id  = get_term_meta( $channel->term_id, Channel::CHANNEL_ID, true );

		if ( in_array( $channel_id, (array) $customer->getChannelIds() ) ) {
			return false;
		}

		do_action( 'bigcommerce/log', Error_Log::ERROR, __( 'Customer does not belong to the current channel. Remove customer', 'bigcommerce' ), [
			'customer_id' => $customer->getId(),
			'channel_id'  => $channel_id
		], 'webhooks' );

		return $this->delete_customer( $customer->getId() );
	}

	/**
	 * @param \WP_User                           $user
	 * @param \BigCommerce\Api\v3\Model\Customer $customer
	 */
	protected function save_customer_channel_data( \WP_User $user, Customer $customer ): void {
		update_user_meta( $user->ID, Customer_Channel_Updater::CUSTOMER_CHANNEL_META, ( array ) $customer->getChannelIds() );
		update_user_meta( $user->ID, Customer_Channel_Updater::CUSTOMER_ORIGIN_CHANNEL, $customer->getOriginChannelId() );
	}

}
