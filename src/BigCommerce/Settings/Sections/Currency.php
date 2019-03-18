<?php


namespace BigCommerce\Settings\Sections;


use BigCommerce\Settings\Screens\Settings_Screen;

class Currency extends Settings_Section {
	const NAME = 'currency';

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
			self::CURRENCY_SYMBOL
		);

		register_setting(
			Settings_Screen::NAME,
			self::CURRENCY_SYMBOL_POSITION
		);

		register_setting(
			Settings_Screen::NAME,
			self::DECIMAL_UNITS
		);

		register_setting(
			Settings_Screen::NAME,
			self::PRICE_DISPLAY
		);


		/**
		 * This filter is documented in src/BigCommerce/Container/Currency.php
		 */
		$auto_format = apply_filters( 'bigcommerce/settings/currency/auto-format', class_exists( '\NumberFormatter' ) );

		if ( ! $auto_format ) {
			// only render currency fields if auto-format is disabled

			add_settings_field(
				self::CURRENCY_SYMBOL,
				esc_html( __( 'Currency Symbol', 'bigcommerce' ) ),
				[ $this, 'render_field', ],
				Settings_Screen::NAME,
				self::NAME,
				[
					'option'    => self::CURRENCY_SYMBOL,
					'type'      => 'text',
					'default'   => '$',
					'label_for' => 'field-' . self::CURRENCY_SYMBOL,
				]
			);

			add_settings_field(
				self::CURRENCY_SYMBOL_POSITION,
				esc_html( __( 'Symbol Position', 'bigcommerce' ) ),
				[ $this, 'render_position_select', ],
				Settings_Screen::NAME,
				self::NAME,
				[
					'label_for' => 'field-' . self::CURRENCY_SYMBOL_POSITION,
				]
			);

			add_settings_field(
				self::DECIMAL_UNITS,
				esc_html( __( 'Decimal Units', 'bigcommerce' ) ),
				[ $this, 'render_field', ],
				Settings_Screen::NAME,
				self::NAME,
				[
					'option'    => self::DECIMAL_UNITS,
					'type'      => 'number',
					'default'   => 2,
					'label_for' => 'field-' . self::DECIMAL_UNITS,
				]
			);

		}

		add_settings_field(
			self::PRICE_DISPLAY,
			esc_html( __( 'Price Display', 'bigcommerce' ) ),
			[ $this, 'render_price_display_field', ],
			Settings_Screen::NAME,
			self::NAME,
			[
				'option'    => self::PRICE_DISPLAY,
				'label_for' => 'field-' . self::PRICE_DISPLAY,
			]
		);
	}

	public function render_section( $section ) {
		/**
		 * This filter is documented in src/BigCommerce/Container/Currency.php
		 */
		$auto_format = apply_filters( 'bigcommerce/settings/currency/auto-format', class_exists( '\NumberFormatter' ) );

		if ( ! $auto_format ) {
			$message = sprintf(
				__( 'These settings affect how your prices are displayed in WordPress, but will not affect the <a href="%s"> BigCommerce currency settings</a> with which shoppers will be charged.', 'bigcommerce' ),
				esc_url( 'https://login.bigcommerce.com/deep-links/settings/currencies' )
			);
			printf( '<p class="description">%s</p>', $message );
		}

		$code = get_option( self::CURRENCY_CODE, '' );
		if ( ! empty( $code ) ) {
			printf(
				'<p>%s</p>',
				sprintf( __( 'Currency code set to %s', 'bigcommerce' ), $code )
			);
		} elseif ( $auto_format ) {
			printf( '<p>%s</p>', __( 'Currency code will be automatically set when the product import completes', 'bigcommerce' ) );
		}

		do_action( 'bigcommerce/settings/render/currency', $section );
	}

	public function render_position_select() {
		$value   = get_option( self::CURRENCY_SYMBOL_POSITION, 'left' );
		$choices = [
			self::POSITION_LEFT        => __( 'Left', 'bigcommerce' ),
			self::POSITION_RIGHT       => __( 'Right', 'bigcommerce' ),
			self::POSITION_LEFT_SPACE  => __( 'Left with space', 'bigcommerce' ),
			self::POSITION_RIGHT_SPACE => __( 'Right with space', 'bigcommerce' ),
		];
		$options = [];
		foreach ( $choices as $key => $label ) {
			$options[] = sprintf( '<option value="%s" %s>%s</option>', esc_attr( $key ), selected( $key, $value, false ), esc_html( $label ) );
		}

		printf( '<select id="field-%s" name="%s" class="regular-text bc-field-choices">%s</select>', esc_attr( self::CURRENCY_SYMBOL_POSITION ), esc_attr( self::CURRENCY_SYMBOL_POSITION ), implode( "\n", $options ) );
	}

	public function render_price_display_field() {
		$value   = get_option( self::PRICE_DISPLAY, self::DISPLAY_TAX_EXCLUSIVE );
		$choices = [
			self::DISPLAY_TAX_EXCLUSIVE => __( 'Excluding Tax', 'bigcommerce' ),
			self::DISPLAY_TAX_INCLUSIVE => __( 'Including Tax', 'bigcommerce' ),
		];

		$options = [];
		foreach ( $choices as $key => $label ) {
			$options[] = sprintf( '<option value="%s" %s>%s</option>', esc_attr( $key ), selected( $key, $value, false ), esc_html( $label ) );
		}
		printf( '<select id="field-%s" name="%s" class="regular-text bc-field-choices">%s</select>', esc_attr( self::PRICE_DISPLAY ), esc_attr( self::PRICE_DISPLAY ), implode( "\n", $options ) );

		printf( '<p class="description">%s</p>', __( 'Choose whether to include tax in prices shown on your store.', 'bigcommerce' ) );
	}

}