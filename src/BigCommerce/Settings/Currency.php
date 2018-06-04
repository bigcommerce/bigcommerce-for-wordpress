<?php


namespace BigCommerce\Settings;


class Currency extends Settings_Section {
	const NAME = 'currency';

	const CURRENCY_CODE            = 'bigcommerce_currency_code';
	const CURRENCY_SYMBOL          = 'bigcommerce_currency_symbol';
	const CURRENCY_SYMBOL_POSITION = 'bigcommerce_currency_symbol_position';
	const DECIMAL_UNITS            = 'bigcommerce_decimal_units';

	const POSITION_LEFT        = 'left';
	const POSITION_RIGHT       = 'right';
	const POSITION_LEFT_SPACE  = 'left+space';
	const POSITION_RIGHT_SPACE = 'right+space';

	/**
	 * @return void
	 * @action bigcommerce/settings/register
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

		/**
		 * This filter is documented in src/BigCommerce/Container/Currency.php
		 */
		$auto_format = apply_filters( 'bigcommerce/settings/currency/auto-format', class_exists( '\NumberFormatter' ) );

		if ( $auto_format ) {
			return; // do not render the setting fields
		}

		add_settings_field(
			self::CURRENCY_SYMBOL,
			esc_html( __( 'Currency Symbol', 'bigcommerce' ) ),
			[ $this, 'render_field', ],
			Settings_Screen::NAME,
			self::NAME,
			[
				'option'  => self::CURRENCY_SYMBOL,
				'type'    => 'text',
				'default' => '$',
			]
		);

		add_settings_field(
			self::CURRENCY_SYMBOL_POSITION,
			esc_html( __( 'Symbol Position', 'bigcommerce' ) ),
			[ $this, 'render_position_select', ],
			Settings_Screen::NAME,
			self::NAME
		);

		add_settings_field(
			self::DECIMAL_UNITS,
			esc_html( __( 'Decimal Units', 'bigcommerce' ) ),
			[ $this, 'render_field', ],
			Settings_Screen::NAME,
			self::NAME,
			[
				'option'  => self::DECIMAL_UNITS,
				'type'    => 'number',
				'default' => 2,
			]
		);
	}

	public function render_section( $section ) {
		$code = get_option( self::CURRENCY_CODE, '' );
		if ( ! empty( $code ) ) {
			printf(
				'<p>%s</p>',
				sprintf( __( 'Currency code set to %s', 'bigcommerce' ), $code )
			);
		} elseif ( class_exists( '\NumberFormatter' ) ) {
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

		printf( '<select name="%s" class="regular-text">%s</select>', esc_attr( self::CURRENCY_SYMBOL_POSITION ), implode( "\n", $options ) );
	}

}