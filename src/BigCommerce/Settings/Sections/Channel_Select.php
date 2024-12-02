<?php


namespace BigCommerce\Settings\Sections;


use BigCommerce\Api\v3\Api\ChannelsApi;
use BigCommerce\Api\v3\ApiException;
use BigCommerce\Api\v3\Model\Channel;
use BigCommerce\Settings\Screens\Connect_Channel_Screen;

class Channel_Select extends Settings_Section {
	const NAME         = 'channel_select';
	const CHANNEL_TERM = 'bigcommerce_channel_term_id';
	const NEW_CHANNEL  = 'create';
	const NEW_NAME     = 'bigcommerce_new_channel_name';

	public function register_settings_section() {

		add_settings_section(
			self::NAME,
			__( 'Channel Settings', 'bigcommerce' ),
			'__return_false',
			Connect_Channel_Screen::NAME
		);

		register_setting(
			Connect_Channel_Screen::NAME,
			self::CHANNEL_TERM
		);
		register_setting(
			Connect_Channel_Screen::NAME,
			self::NEW_NAME,
			[ 'sanitize_callback' => [ $this, 'sanitize_channel_name' ] ]
		);

		add_settings_field(
			self::CHANNEL_TERM,
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
				'default' => $this->sanitize_channel_name( parse_url( home_url(), PHP_URL_HOST ) ),
				'class'   => 'bc-create-channel-wrapper',
			]
		);
	}

	public function render_channel_select_field() {
		$selected = 0;
		$options  = $this->get_channel_list();
		array_unshift( $options, [ 'id' => 'create', 'name' => __( 'Create a New Channel', 'bigcommerce' ) ] );
		if ( count( $options ) > 1 ) {
			array_unshift( $options, [ 'id' => 0, 'name' => __( 'Select a channel', 'bigcommerce' ) ] );
		}
		$options = array_map( function ( $channel ) use ( $selected ) {
			return sprintf( '<option value="%s" %s>%s</option>', $channel['id'], selected( $channel['id'], $selected, false ), esc_html( $channel['name'] ) );
		}, $options );
		printf( '<p><select name="%s" class="regular-text" data-js="bc-settings__channel-select">%s</select></p>', esc_attr( self::CHANNEL_TERM ), implode( $options ) );
		do_action( 'bigcommerce/settings/render/channel_select' );
	}

	private function get_channel_list() {
		$terms = get_terms( [
			'taxonomy'   => \BigCommerce\Taxonomies\Channel\Channel::NAME,
			'hide_empty' => false,
			'orderby'    => 'name',
			'order'      => 'ASC',
		] );
		$list  = array_map( function ( \WP_Term $channel ) {
			return [
				'id'   => $channel->term_id,
				'name' => $channel->name,
			];
		}, $terms );

		return $list;
	}

	/**
	 * Sanitize the channel name before saving or using
	 *
	 * @param string $name The channel name to sanitize
	 * @return string The sanitized channel name
	 */
	public function sanitize_channel_name( $name ) {
		if ( empty( $name ) ) {
			$name = parse_url( home_url(), PHP_URL_HOST );
		}
		$name = str_replace( '.', '-', $name );
		return trim( $name );
	}

	/**
	 * Override parent render_field to ensure channel name is sanitized
	 */
	public function render_field( $args ) {
		if ($args['option'] === self::NEW_NAME) {
			$args['default'] = $this->sanitize_channel_name($args['default']);
			if (isset($_POST[self::NEW_NAME])) {
				$_POST[self::NEW_NAME] = $this->sanitize_channel_name($_POST[self::NEW_NAME]);
			}
		}
		parent::render_field($args);
	}
}
