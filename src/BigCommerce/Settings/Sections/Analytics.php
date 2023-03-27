<?php


namespace BigCommerce\Settings\Sections;


use BigCommerce\Api\Store_Api;
use BigCommerce\Api\v3\Api\SettingsApi;
use BigCommerce\Settings\Screens\Settings_Screen;
use BigCommerce\Taxonomies\Channel\Channel;
use BigCommerce\Taxonomies\Channel\Connections;

class Analytics extends Settings_Section {
	const NAME          = 'analytics';
	const TRACK_BY_HOOK = 'bigcommerce/analytics/track_by';

	const SYNC_ANALYTICS    = 'bigcommerce_sync_analytics';
	const FACEBOOK_PIXEL    = 'bigcommerce_facebook_pixel_id';
	const GOOGLE_ANALYTICS  = 'bigcommerce_google_analytics_id';
	const TRACK_PRODUCT_SKU = 'bigcommerce_track_product_sku';
	const SEGMENT           = 'bigcommerce_segment_key';

	const FACEBOOK_PIXEL_NAME   = 'Facebook Pixel';
	const GOOGLE_ANALYTICS_NAME = 'Google Analytics';

	/**
	 * @var Store_Api
	 */
	private $api;

	/**
	 * @var SettingsApi
	 */
	private $api_v3;

	public function __construct( Store_Api $api, SettingsApi $api_v3 ) {
		$this->api    = $api;
		$this->api_v3 = $api_v3;
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
			self::SYNC_ANALYTICS,
			[ 'default' => 1 ]
		);

		register_setting(
			Settings_Screen::NAME,
			self::FACEBOOK_PIXEL
		);

		register_setting(
			Settings_Screen::NAME,
			self::GOOGLE_ANALYTICS
		);

		register_setting( Settings_Screen::NAME, self::TRACK_PRODUCT_SKU );


		add_settings_field(
			self::SYNC_ANALYTICS,
			esc_html( __( 'Sync Tracking IDs', 'bigcommerce' ) ),
			[ $this, 'render_checkbox', ],
			Settings_Screen::NAME,
			self::NAME,
			[
				'option'      => self::SYNC_ANALYTICS,
				'type'        => 'text',
				'default'     => 1,
				'description' => __( 'Disable the sync to set different tracking IDs on different sites connected to your account.', 'bigcommerce' ),
				'label'       => __( 'Keep analytics tracking IDs in sync with your BigCommerce store settings', 'bigcommerce' ),
			]
		);

		add_settings_field(
			self::TRACK_PRODUCT_SKU,
			esc_html( __( 'Track SKU Data', 'bigcommerce' ) ),
			[ $this, 'render_checkbox', ],
			Settings_Screen::NAME,
			self::NAME,
			[
				'option'  => self::TRACK_PRODUCT_SKU,
				'type'    => 'text',
				'default' => 0,
				'label'   => __( 'Include SKU data in analytics ', 'bigcommerce' ),
			]
		);
		add_settings_field(
			self::FACEBOOK_PIXEL,
			esc_html( __( 'Facebook Pixel ID', 'bigcommerce' ) ),
			[ $this, 'render_field', ],
			Settings_Screen::NAME,
			self::NAME,
			[
				'option'    => self::FACEBOOK_PIXEL,
				'type'      => 'text',
				'label_for' => 'field-' . self::FACEBOOK_PIXEL,
			]
		);
		add_settings_field(
			self::GOOGLE_ANALYTICS,
			esc_html( __( 'Google Analytics Tracking ID', 'bigcommerce' ) ),
			[ $this, 'render_field', ],
			Settings_Screen::NAME,
			self::NAME,
			[
				'option'    => self::GOOGLE_ANALYTICS,
				'type'      => 'text',
				'label_for' => 'field-' . self::GOOGLE_ANALYTICS,
			]
		);
	}

	public function render_checkbox( $args ) {
		$option      = $args['option'];
		$label       = $args['label'];
		$description = $args['description'] ?? '';
		$default     = $args['default'] ?? '';
		$value       = (bool) get_option( $option, $default );

		printf(
			'<label><input id="field-%s" type="checkbox" value="1" class="regular-text code" name="%s" %s /> %s</label>',
			esc_attr( $option ),
			esc_attr( $option ),
			checked( true, $value, false ),
			esc_html( $label )
		);

		if ( empty( $description ) ) {
			return;
		}
		printf( '<p class="description">%s</p>', esc_html( $description ) );
	}

	/**
	 * @param string $old_value
	 * @param string $new_value
	 *
	 * @return void
	 * @action update_option_ . self::FACEBOOK_PIXEL
	 */
	public function update_pixel_option( $old_value, $new_value ) {
		if ( $old_value == $new_value || ! get_option( self::SYNC_ANALYTICS, 1 ) ) {
			return;
		}
		$connections = new Connections();
		$channel     = $connections->current();
		$settings    = $this->api_v3->getStoreAnalyticsSettings( (int) get_term_meta( $channel->term_id, Channel::CHANNEL_ID, true ) );

		if ( empty( $settings ) || empty( $settings->data ) ) {
			return;
		}

		$settings = json_decode( json_encode( $settings->data ), true );

		foreach ( $settings as &$account ) {
			if ( $account['name'] == self::FACEBOOK_PIXEL_NAME ) {
				$account['code']    = $new_value;
				$account['enabled'] = ! empty( $new_value );
				$this->api->update_analytics_settings( $account['id'], $account );

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
		if ( $old_value == $new_value || ! get_option( self::SYNC_ANALYTICS, 1 ) ) {
			return;
		}
		$connections = new Connections();
		$channel     = $connections->current();
		$settings    = $this->api_v3->getStoreAnalyticsSettings( (int) get_term_meta( $channel->term_id, Channel::CHANNEL_ID, true ) );

		if ( empty( $settings ) || empty( $settings->data ) ) {
			return;
		}

		$settings = json_decode( json_encode( $settings->data ), true );

		foreach ( $settings as &$account ) {
			if ( $account['name'] == self::GOOGLE_ANALYTICS_NAME ) {
				if ( strpos( $account['code'], 'script' ) === false ) {
					$account['code']    = $new_value;
					$account['enabled'] = ! empty( $new_value );
					$this->api->update_analytics_settings( $account['id'], $account );
				}

				return;
			}
		}
	}
}
