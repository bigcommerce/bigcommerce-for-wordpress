<?php


namespace BigCommerce\Accounts;

use BigCommerce\Accounts\Customer;
use BigCommerce\Api\v3\Api\CustomersApi;
use BigCommerce\Taxonomies\Channel\Channel;
use BigCommerce\Taxonomies\Channel\Connections;
use BigCommerce\Settings\Sections\Account_Settings;


class Channel_Settings {

	const RESYNC_TIME = 600;

	protected $channel_id;

	/**
	 * @var Connections
	 */
	protected $connections;
	
	/**
	 * @var CustomersApi
	 */
	protected $customers;

	public function __construct( Connections $connections, CustomersApi $customers ) {
		$this->connections = $connections;
		$this->customers   = $customers;
	}

	protected function get_channel_id() {
		if ( ! $this->channel_id ) {
			try {
				$current_channel = $this->connections->current();
				if ( $current_channel ) {
					$this->channel_id = (int) get_term_meta( $current_channel->term_id, Channel::CHANNEL_ID, true );
				}
			} catch (\Exception $e) {
				
			}
		}

		return $this->channel_id;
	}
	
	/**
	 * @return void
	 * @action bigcommerce/sync_global_logins
	 */
	public function sync_global_logins() {
		$channel_id = $this->get_channel_id();
		if ( ! $channel_id ) {
			// Do this another time when channel is connected
			$this->schedule_resync();
			return;
		}

		
		try {
			$allow_global_logins = (bool) get_option( Account_Settings::ALLOW_GLOBAL_LOGINS, true );
			
			$response = $this->customers->updateCustomerSettings( $channel_id, [
				'allow_global_logins' => $allow_global_logins
			] );

			// If action was successful
			if ( $response->getData()->getAllowGlobalLogins() === $allow_global_logins ) {
				$this->clear_all_scheduled_events();
			} else {
				$this->schedule_resync();
			}

		} catch ( \Exception $e ) {
			$this->schedule_resync();
		}
	}

	/**
	 * @return void
	 */
	protected function schedule_resync() {
		$this->clear_all_scheduled_events();
		wp_schedule_single_event( time() + self::RESYNC_TIME, 'bigcommerce/sync_global_logins' );
	}

	/**
	 * @return void
	 * @action bigcommerce/channel/promote
	 */
	public function schedule_sync() {
		// Turn on the option for new channels
		// Avoid triggering the update_option listener
		delete_option( Account_Settings::ALLOW_GLOBAL_LOGINS );
		add_option( Account_Settings::ALLOW_GLOBAL_LOGINS, '1' );

		$this->clear_all_scheduled_events();
		wp_schedule_single_event( time(), 'bigcommerce/sync_global_logins' );
	}

	/**
	 * @return void
	 */
	protected function clear_all_scheduled_events() {
		wp_clear_scheduled_hook( 'bigcommerce/sync_global_logins' );
	}

}
