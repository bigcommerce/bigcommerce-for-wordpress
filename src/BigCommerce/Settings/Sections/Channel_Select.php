<?php


namespace BigCommerce\Settings\Sections;


use BigCommerce\Api\v3\Api\ChannelsApi;
use BigCommerce\Api\v3\ApiException;
use BigCommerce\Api\v3\Model\Channel;
use BigCommerce\Settings\Screens\Connect_Channel_Screen;

class Channel_Select extends Settings_Section {
	const NAME        = 'channel_select';
	const NEW_CHANNEL = 'create';
	const NEW_NAME    = 'bigcommerce_new_channel_name';

	/**
	 * @var ChannelsApi
	 */
	private $api;

	public function __construct( ChannelsApi $api ) {
		$this->api = $api;
	}

	public function register_settings_section() {

		add_settings_section(
			self::NAME,
			__( 'Channel Settings', 'bigcommerce' ),
			'__return_false',
			Connect_Channel_Screen::NAME
		);

		register_setting(
			Connect_Channel_Screen::NAME,
			Channels::CHANNEL_ID
		);
		register_setting(
			Connect_Channel_Screen::NAME,
			self::NEW_NAME,
			'__return_false'
		);

		add_settings_field(
			Channels::CHANNEL_ID,
			esc_html( __( 'Select Channel', 'bigcommerce' ) ),
			[ $this, 'render_channel_select_field' ],
			Connect_Channel_Screen::NAME,
			self::NAME
		);

		add_settings_field(
			self::NEW_NAME,
			esc_html( __( 'New Channel Name', 'bigcommerce' ) ),
			[ $this, 'render_field' ],
			Connect_Channel_Screen::NAME,
			self::NAME,
			[
				'type'    => 'text',
				'option'  => self::NEW_NAME,
				'default' => parse_url( home_url(), PHP_URL_HOST ),
				'class'   => 'bc-create-channel-wrapper',
			]
		);
	}

	public function render_channel_select_field() {
		$selected = 0;
		$options  = $this->get_channel_list();
		array_unshift( $options, [ 'id' => 'create', 'name' => __( 'Create a New Channel', 'bigcommerce' ) ] );
		array_unshift( $options, [ 'id' => 0, 'name' => __( 'Select a channel', 'bigcommerce' ) ] );
		$options = array_map( function ( $channel ) use ( $selected ) {
			return sprintf( '<option value="%s" %s>%s</option>', $channel[ 'id' ], selected( $channel[ 'id' ], $selected, false ), esc_html( $channel[ 'name' ] ) );
		}, $options );
		printf( '<p><select name="%s" class="regular-text" data-js="bc-settings__channel-select">%s</select></p>', esc_attr( Channels::CHANNEL_ID ), implode( $options ) );
		do_action( 'bigcommerce/settings/render/channel_select' );
	}

	private function get_channel_list() {
		try {
			$channels = $this->api->listChannels()->getData();
		} catch ( ApiException $e ) {
			$channels = [];
		}
		$channels = array_filter( $channels, function ( Channel $channel ) {
			return $channel->getPlatform() === "wordpress";
		} );
		$list     = array_map( function ( Channel $channel ) {
			return [
				'id'   => $channel->getId(),
				'name' => $channel->getName(),
			];
		}, $channels );
		usort( $list, function ( $a, $b ) {
			return strcmp( $a[ 'name' ], $b[ 'name' ] );
		} );

		return $list;
	}
}