<?php


namespace BigCommerce\Settings\Sections;


use BigCommerce\Api\Store_Api;
use BigCommerce\Settings\Screens\Settings_Screen;

class Analytics extends Settings_Section {
	const NAME = 'analytics';

	const FACEBOOK_PIXEL   = 'bigcommerce_facebook_pixel_id';
	const GOOGLE_ANALYTICS = 'bigcommerce_google_analytics_id';
	const SEGMENT          = 'bigcommerce_segment_key';

	/**
	 * @var Store_Api
	 */
	private $api;

	public function __construct( Store_Api $api ) {
		$this->api = $api;
	}

	/**
	 * @return void
	 * @action bigcommerce/settings/register/screen= . Settings_Screen::NAME
	 */
	public function register_settings_section() {
		add_settings_section(
			self::NAME,
			__( 'Analytics Settings', 'bigcommerce' ),
			function ( $section ) {
				do_action( 'bigcommerce/settings/render/analytics', $section );
			},
			Settings_Screen::NAME
		);

		register_setting(
			Settings_Screen::NAME,
			self::FACEBOOK_PIXEL
		);

		register_setting(
			Settings_Screen::NAME,
			self::GOOGLE_ANALYTICS
		);


		add_settings_field(
			self::FACEBOOK_PIXEL,
			esc_html( __( 'Facebook Pixel ID', 'bigcommerce' ) ),
			[ $this, 'render_field', ],
			Settings_Screen::NAME,
			self::NAME,
			[
				'option' => self::FACEBOOK_PIXEL,
				'type'   => 'text',
			]
		);
		add_settings_field(
			self::GOOGLE_ANALYTICS,
			esc_html( __( 'Google Analytics Tracking ID', 'bigcommerce' ) ),
			[ $this, 'render_field', ],
			Settings_Screen::NAME,
			self::NAME,
			[
				'option' => self::GOOGLE_ANALYTICS,
				'type'   => 'text',
			]
		);
	}

	/**
	 * @param string $old_value
	 * @param string $new_value
	 *
	 * @return void
	 * @action update_option_ . self::FACEBOOK_PIXEL
	 */
	public function update_pixel_option( $old_value, $new_value ) {
		if ( $old_value == $new_value ) {
			return;
		}
		$settings = $this->api->get_analytics_settings();
		foreach ( $settings as &$account ) {
			if ( $account[ 'name' ] == 'Facebook Pixel' ) {
				$account[ 'code' ]    = $new_value;
				$account[ 'enabled' ] = ! empty( $new_value );
				$this->api->update_analytics_settings( $account[ 'id' ], $account );

				return;
			}
		}
	}


	/**
	 * @param string $old_value
	 * @param string $new_value
	 *
	 * @return void
	 * @action update_option_ . self::GOOGLE_ANALYTICS
	 */
	public function update_google_option( $old_value, $new_value ) {
		if ( $old_value == $new_value ) {
			return;
		}
		// TODO
	}
}