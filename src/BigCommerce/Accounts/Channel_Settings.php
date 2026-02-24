<?php


namespace BigCommerce\Accounts;

use BigCommerce\Accounts\Customer;
use BigCommerce\Api\v3\Api\CustomersApi;
use BigCommerce\Taxonomies\Channel\Channel;
use BigCommerce\Taxonomies\Channel\Connections;
use BigCommerce\Settings\Sections\Account_Settings;

/**
 * Manages channel settings related to global logins, syncing customer settings, 
 * and scheduling resyncs for updated global login configurations.
 */
class Channel_Settings {

	/** @const int RESYNC_TIME The time interval in seconds to resync global logins. */
	const RESYNC_TIME = 600;

	/** @var int The channel ID. */
	protected $channel_id;

	/** @var Connections The Connections instance for managing channels. */
	protected $connections;

	/** @var CustomersApi The Customers API instance for updating customer settings. */
	protected $customers;

	/**
	 * Channel_Settings constructor.
	 *
	 * @param Connections $connections The Connections instance for managing channels.
	 * @param CustomersApi $customers The Customers API instance for updating customer settings.
	 */
	public function __construct( Connections $connections, CustomersApi $customers ) {
		$this->connections = $connections;
		$this->customers   = $customers;
	}

	/**
	 * Get the current channel ID.
	 *
	 * Retrieves the channel ID associated with the current active channel from the Connections instance.
	 * 
	 * @return int The channel ID, or 0 if no channel is found.
	 */
	protected function get_channel_id() {
		if ( ! $this->channel_id ) {
			try {
				$current_channel = $this->connections->current();
				if ( $current_channel ) {
					$this->channel_id = (int) get_term_meta( $current_channel->term_id, Channel::CHANNEL_ID, true );
				}
			} catch ( \Exception $e ) {
			}
		}

		return $this->channel_id;
	}

	/**
	 * Sync global logins for the current channel.
	 *
	 * Updates the global login setting for the current channel. If the channel ID is unavailable,
	 * it will schedule a resync for later.
	 * 
	 * @return void
	 * @action bigcommerce/sync_global_logins Triggered when syncing global logins.
	 */
	public function sync_global_logins() {
		$channel_id = $this->get_channel_id();
		if ( ! $channel_id ) {
			// Do this another time when the channel is connected
			$this->schedule_resync();
			return;
		}

		try {
			$allow_global_logins = (bool) get_option( Account_Settings::ALLOW_GLOBAL_LOGINS, true );

			$response = $this->customers->updateCustomerSettings( $channel_id, [
				'allow_global_logins' => $allow_global_logins
			] );

			// If the action was successful
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
	 * Schedule a resync for global logins.
	 *
	 * Schedules a resync of global logins by setting a single event to trigger
	 * after a predefined interval (RESYNC_TIME).
	 *
	 * @return void
	 */
	protected function schedule_resync() {
		$this->clear_all_scheduled_events();
		wp_schedule_single_event( time() + self::RESYNC_TIME, 'bigcommerce/sync_global_logins' );
	}

	/**
	 * Schedule a sync for the channel.
	 *
	 * Triggers a sync for global logins by turning on the option for new channels,
	 * avoiding triggering the update_option listener, and scheduling the sync.
	 *
	 * @return void
	 * @action bigcommerce/channel/promote Triggered when promoting a channel.
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
	 * Clear all scheduled events for syncing global logins.
	 *
	 * Clears the scheduled events for the "bigcommerce/sync_global_logins" hook.
	 * 
	 * @return void
	 */
	protected function clear_all_scheduled_events() {
		wp_clear_scheduled_hook( 'bigcommerce/sync_global_logins' );
	}

}
