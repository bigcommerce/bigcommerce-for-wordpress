<?php


namespace BigCommerce\Settings\Sections;


use BigCommerce\Settings\Screens\Settings_Screen;

/**
 * Class Channels
 *
 * Channel configuration after the plugin has been fully configured,
 * which at this point is limited to changing the channel name
 */
class Channels extends Settings_Section {
	const NAME         = 'channel';
	const CHANNEL_ID   = 'bigcommerce_channel_id';
	const CHANNEL_NAME = 'bigcommerce_channel_name';



	public function register_settings_section() {

		add_settings_section(
			self::NAME,
			__( 'Channel', 'bigcommerce' ),
			'__return_false',
			Settings_Screen::NAME
		);

		register_setting(
			Settings_Screen::NAME,
			self::CHANNEL_NAME
		);

		add_settings_field(
			self::CHANNEL_NAME,
			esc_html( __( 'Channel Name', 'bigcommerce' ) ),
			[ $this, 'render_field' ],
			Settings_Screen::NAME,
			self::NAME,
			[
				'type'    => 'text',
				'option'  => self::CHANNEL_NAME,
			]
		);
	}
}