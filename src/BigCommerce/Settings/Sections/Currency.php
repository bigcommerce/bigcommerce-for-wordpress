<?php


namespace BigCommerce\Settings\Sections;


use BigCommerce\Exceptions\Channel_Not_Found_Exception;
use BigCommerce\Settings\Screens\Settings_Screen;
use BigCommerce\Taxonomies\Channel;

class Currency extends Settings_Section {
	const NAME = 'currency';

	const ENABLED_CURRENCIES = 'bigcommerce_enabled_currencies';

	const CHANNEL_CURRENCY_CODE    = 'bigcommerce_channel_currency_code';
	const CHANNEL_ALLOWED_CURRENCY = 'bigcommerce_allowed_channel_currencies';

	const CURRENCY_CODE            = 'bigcommerce_currency_code';
	const CURRENCY_SYMBOL          = 'bigcommerce_currency_symbol';
	const CURRENCY_SYMBOL_POSITION = 'bigcommerce_currency_symbol_position';
	const INTEGER_UNITS            = 'bigcommerce_integer_units';
	const DECIMAL_UNITS            = 'bigcommerce_decimal_units';
	const PRICE_DISPLAY            = 'bigcommerce_price_display';

	const POSITION_LEFT        = 'left';
	const POSITION_RIGHT       = 'right';
	const POSITION_LEFT_SPACE  = 'left+space';
	const POSITION_RIGHT_SPACE = 'right+space';

	const DISPLAY_TAX_INCLUSIVE = 'tax_inclusive';
	const DISPLAY_TAX_EXCLUSIVE = 'tax_exclusive';

	const ENABLE_CURRENCY_SWITCHER = 'enable_currency_switcher';

	/**
	 * @return void
	 * @action bigcommerce/settings/register/screen= . Settings_Screen::NAME
	 */
	public function register_settings_section() {
		add_settings_section(
			self::NAME,
			__( 'Currency Settings', 'bigcommerce' ),
			[ $this, 'render_section' ],
			Settings_Screen::NAME
		);

		register_setting(
			Settings_Screen::NAME,
			self::PRICE_DISPLAY
		);

		add_settings_field(
			self::PRICE_DISPLAY,
			esc_html( __( 'Price Display', 'bigcommerce' ) ),
			[ $this, 'render_price_display_field' ],
			Settings_Screen::NAME,
			self::NAME,
			[
				'option'    => self::PRICE_DISPLAY,
				'label_for' => 'field-' . self::PRICE_DISPLAY,
			]
		);

		register_setting(
			Settings_Screen::NAME,
			self::ENABLE_CURRENCY_SWITCHER
		);

		add_settings_field(
			self::ENABLE_CURRENCY_SWITCHER,
			esc_html( __( 'Enable Currency Switcher', 'bigcommerce' ) ),
			[ $this, 'render_enable_currency_switcher', ],
			Settings_Screen::NAME,
			self::NAME,
			[
				'label_for' => 'field-' . self::ENABLE_CURRENCY_SWITCHER,
			]
		);
	}

	public function render_section( $section ) {
		$message = sprintf(
			__( 'Available currencies are configured in your <a href="%s"> BigCommerce currency settings</a>.', 'bigcommerce' ),
			esc_url( 'https://login.bigcommerce.com/deep-links/settings/currencies' )
		);
		printf( '<p class="description">%s</p>', $message );

		do_action( 'bigcommerce/settings/render/currency', $section );
	}

	public function render_price_display_field() {
		$value   = get_option( self::PRICE_DISPLAY, self::DISPLAY_TAX_EXCLUSIVE );
		$choices = [
			self::DISPLAY_TAX_EXCLUSIVE => esc_html( __( 'Excluding Tax', 'bigcommerce' ) ),
			self::DISPLAY_TAX_INCLUSIVE => esc_html( __( 'Including Tax', 'bigcommerce' ) ),
		];

		$options = [];
		foreach ( $choices as $key => $label ) {
			$options[] = sprintf( '<option value="%s" %s>%s</option>', esc_attr( $key ), selected( $key, $value, false ), esc_html( $label ) );
		}
		printf( '<select id="field-%s" name="%s" class="regular-text bc-field-choices">%s</select>', esc_attr( self::PRICE_DISPLAY ), esc_attr( self::PRICE_DISPLAY ), implode( "\n", $options ) );

		printf( '<p class="description">%s</p>', esc_html( __( 'Choose whether to include tax in prices shown on your store.', 'bigcommerce' ) ) );
	}

	public function render_enable_currency_switcher() {
		$value    = (bool) get_option( self::ENABLE_CURRENCY_SWITCHER, false );
		$checkbox = sprintf( '<input id="field-%s" type="checkbox" value="1" class="regular-text code" name="%s" %s />', esc_attr( self::ENABLE_CURRENCY_SWITCHER ), esc_attr( self::ENABLE_CURRENCY_SWITCHER ), checked( true, $value, false ) );
		printf( '<p class="description">%s %s</p>', $checkbox, esc_html( __( 'If enabled, this adds a WordPress Widget to allow customers to switch the currency on the front-end.', 'bigcommerce' ) ) );
	}

}
