<?php


namespace BigCommerce\Merchant;


use BigCommerce\Api\v3\Api\ChannelsApi;
use BigCommerce\Api\v3\ApiException;
use BigCommerce\Api\v3\Model\Channel;
use BigCommerce\Api\v3\Model\CreateChannelRequest;
use BigCommerce\Api\v3\Model\UpdateChannelRequest;
use BigCommerce\Settings\Sections\Channel_Select;
use BigCommerce\Settings\Sections\Channels;

/**
 * Class Channel_Connector
 *
 * Responsible for connecting the WordPress site with
 * a BigCommerce channel
 */
class Channel_Connector {
	/**
	 * @var ChannelsApi
	 */
	private $channels;

	public function __construct( ChannelsApi $channels_api ) {
		$this->channels = $channels_api;
	}

	private function create_channel( $name ) {
		$request = new CreateChannelRequest( [
			'type'     => 'storefront',
			'platform' => 'wordpress',
			'name'     => $name,
		] );
		try {
			$response = $this->channels->createChannel( $request );
		} catch ( ApiException $e ) {
			do_action( 'bigcommerce/channel/error/could_not_create_channel', $e, $request );

			return 0;
		}
		$channel = $response->getData();
		update_option( Channels::CHANNEL_ID, $channel->getId() );
		update_option( Channels::CHANNEL_NAME, $channel->getName() );

		return $channel->getId();
	}

	private function update_channel_name( $channel_id, $name ) {
		$request = new UpdateChannelRequest( [
			'type'     => 'storefront',
			'platform' => 'wordpress',
			'name'     => $name,
		] );
		try {
			$response = $this->channels->updateChannel( $channel_id, $request );
		} catch ( ApiException $e ) {
			do_action( 'bigcommerce/channel/error/could_not_update_channel', $e, $request, $channel_id );

			return false;
		}
		$channel = $response->getData();

		return $channel->getName();
	}

	/**
	 * @param string|int $value
	 *
	 * @return int
	 * @filter sanitize_option_ . Channels::CHANNEL_ID
	 */
	public function handle_connect_request( $value ) {
		if ( $value === Channel_Select::NEW_CHANNEL ) {
			if ( ! empty( $_POST[ Channel_Select::NEW_NAME ] ) ) {
				$name = sanitize_text_field( $_POST[ Channel_Select::NEW_NAME ] );
			} else {
				$name = parse_url( home_url(), PHP_URL_HOST );
			}
			$value = $this->create_channel( $name );
			if ( empty( $value ) ) {
				add_settings_error( Channel_Select::NEW_CHANNEL, 'could_not_create_channel', __( 'An error occurred creating your channel', 'bigcommerce' ) );
				set_transient( 'settings_errors', get_settings_errors(), 30 );
			}
		}

		return (int) $value;
	}

	/**
	 * @param $old_channel_id
	 * @param $new_channel_id
	 *
	 * @return void
	 * @action update_option_ . Channels::CHANNEL_ID
	 * @action add_option_ . Channels::CHANNEL_ID
	 */
	public function handle_channel_updated( $old_channel_id, $new_channel_id ) {
		if ( empty( $new_channel_id ) ) {
			return;
		}
		try {
			$channel = $this->channels->getChannel( $new_channel_id )->getData();
			$name    = $channel->getName();
			update_option( Channels::CHANNEL_NAME, $name );
		} catch ( ApiException $e ) {
			return;
		}
		/**
		 * Triggered when the channel ID associated with the site changes.
		 *
		 * @param int $new_channel_id The ID of the new channel
		 * @param int $old_channel_id The ID of the old channel
		 */
		do_action( 'bigcommerce/channel/updated_channel_id', $new_channel_id, $old_channel_id );
	}

	/**
	 * @param string|int $new_value
	 *
	 * @return int
	 * @filter sanitize_option_ . Channels::CHANNEL_NAME
	 */
	public function handle_rename_request( $new_value ) {
		$current    = get_option( Channels::CHANNEL_NAME, '' );
		$channel_id = get_option( Channels::CHANNEL_ID, 0 );
		if ( empty( $channel_id ) || $current === $new_value ) {
			return $new_value;
		}
		$new_value = sanitize_text_field( $new_value );
		if ( empty( $new_value ) ) {
			return $current;
		}
		$name = $this->update_channel_name( $channel_id, $new_value );
		if ( $name ) {
			return $name;
		}

		add_settings_error( Channels::CHANNEL_NAME, 'could_not_update_channel', __( 'An error occurred renaming your channel', 'bigcommerce' ) );
		set_transient( 'settings_errors', get_settings_errors(), 30 );

		return $current;
	}

	/**
	 * Once connected to a channel, the merchant should not be able to edit the store
	 * URL from the API credentials settings.
	 *
	 * @param bool|string $disabled_message
	 *
	 * @return bool|string
	 * @filter bigcommerce/settings/api/disabled/field= . Api_Credentials::OPTION_STORE_URL
	 */
	public function prevent_store_url_changes( $disabled_message ) {
		$channel_id = get_option( Channels::CHANNEL_ID, 0 );
		if ( empty( $channel_id ) ) {
			return $disabled_message;
		}
		$disabled_message = __( 'API Path cannot be changed once connected to a channel.', 'bigcommerce' );
		return $disabled_message;
	}
}