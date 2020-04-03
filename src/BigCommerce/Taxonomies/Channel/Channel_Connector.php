<?php


namespace BigCommerce\Taxonomies\Channel;


use BigCommerce\Api\v3\Api\ChannelsApi;
use BigCommerce\Api\v3\ApiException;
use BigCommerce\Api\v3\Model\Channel as ApiChannel;
use BigCommerce\Api\v3\Model\CreateChannelRequest;
use BigCommerce\Settings\Sections\Channel_Select;
use BigCommerce\Settings\Sections\Channels;
use BigCommerce\Settings\Sections\Import;

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

	public function create_first_channel() {
		$channel_id = get_option( Channels::CHANNEL_ID, 0 );
		if ( ! empty( $channel_id ) ) {
			return; // Already connected to a channel
		}
		try {
			$channels = $this->channels->listChannels()->getData();
		} catch ( ApiException $e ) {
			$channels = [];
		}
		$channels = array_filter( $channels, function ( ApiChannel $channel ) {
			return $channel->getPlatform() === 'wordpress';
		} );
		if ( count( $channels ) > 0 ) {
			return; // A channel already exists. Do not automatically create one.
		}

		/**
		 * Filter the name given to the auto-created channel.
		 * Defaults to the blog's domain name.
		 *
		 * @param string $name The default channel name
		 */
		$channel_name = apply_filters( 'bigcommerce/channel/default_name', parse_url( home_url(), PHP_URL_HOST ) );
		$term_id = $this->create_channel( $channel_name );

		if ( $term_id ) {
			$term = get_term( $term_id );
			/**
			 * Triggers the promotion of a channel to the "Primary" state
			 *
			 * @param \WP_Term $term The Channel term associated with the BigCommerce channel
			 */
			do_action( 'bigcommerce/channel/promote', $term );
		}
		update_option( Import::OPTION_NEW_PRODUCTS, 1 );
		update_option( Import::ENABLE_WEBHOOKS, false );
	}

	/**
	 * Create a new Channel on the BigCommerce store
	 *
	 * @param string $name
	 *
	 * @return int The ID of the WordPress term connected to that channel ID
	 */
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
		$term    = wp_insert_term( $channel->getName(), Channel::NAME );
		if ( is_wp_error( $term ) ) {
			return 0;
		}
		update_term_meta( $term['term_id'], Channel::CHANNEL_ID, $channel->getId() );
		update_term_meta( $term['term_id'], Channel::STATUS, Channel::STATUS_DISCONNECTED );

		return $term['term_id'];
	}

	/**
	 * Handle a request to connect a new channel during onboarding
	 *
	 * @param string|int $value
	 *
	 * @return bool
	 * @filter sanitize_option_ . Channel_Select::CHANNEL_TERM
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

		if ( $value ) {
			$term = get_term( $value );
			/**
			 * Triggers the promotion of a channel to the "Primary" state
			 *
			 * @param \WP_Term $term The Channel term associated with the BigCommerce channel
			 */
			do_action( 'bigcommerce/channel/promote', $term );
		}

		return false;
	}

	/**
	 * @param string|int $value
	 *
	 * @return bool
	 * @filter sanitize_option_ . Channels::NEW_NAME
	 */
	public function handle_create_request( $value ) {
		$name = sanitize_text_field( $value );
		if ( empty( $name ) ) {
			return null;
		}
		$term_id = $this->create_channel( $name );
		if ( empty( $term_id ) ) {
			add_settings_error( Channel_Select::NEW_CHANNEL, 'could_not_create_channel', __( 'An error occurred creating your channel', 'bigcommerce' ) );
			set_transient( 'settings_errors', get_settings_errors(), 30 );
		}

		return false;
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
