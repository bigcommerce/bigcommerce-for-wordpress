<?php


namespace BigCommerce\Settings\Sections;


use BigCommerce\Exceptions\Channel_Not_Found_Exception;
use BigCommerce\Settings\Screens\Settings_Screen;
use BigCommerce\Taxonomies\Channel;

class Currency extends Settings_Section {
	const NAME = 'currency';

	const ENABLED_CURRENCIES = 'bigcommerce_enabled_currencies';

	const CHANNEL_CURRENCY_CODE = 'bigcommerce_channel_currency_code';

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

		try {
			// register a separate currency field for each connected channel
			$connections   = new Channel\Connections();
			$active        = $connections->active();
			$channel_count = count( $active );
			foreach ( $active as $channel ) {
				add_settings_field(
					self::CHANNEL_CURRENCY_CODE . '-' . $channel->term_id,
					$channel_count === 1 ? __( 'Currency', 'bigcommerce' ) : esc_html( $channel->name ),
					[ $this, 'render_currency_select' ],
					Settings_Screen::NAME,
					self::NAME,
					[
						'channel'   => $channel->term_id,
						'count'     => $channel_count,
						'label_for' => 'field-' . self::CHANNEL_CURRENCY_CODE . '-' . $channel->term_id,
					]
				);
			}

			// we'll handle the save collectively
			register_setting(
				Settings_Screen::NAME,
				self::CHANNEL_CURRENCY_CODE,
				[
					'sanitize_callback' => [ $this, 'save_channel_currencies' ],
				]
			);
		} catch ( Channel_Not_Found_Exception $e ) {
			add_settings_field(
				self::CHANNEL_CURRENCY_CODE,
				__( 'Currency', 'bigcommerce' ),
				[ $this, 'render_default_currency_symbol' ],
				Settings_Screen::NAME,
				self::NAME
			);
		}


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
	}

	public function render_section( $section ) {
		$message = sprintf(
			__( 'Available currencies are configured in your <a href="%s"> BigCommerce currency settings</a>.', 'bigcommerce' ),
			esc_url( 'https://login.bigcommerce.com/deep-links/settings/currencies' )
		);
		printf( '<p class="description">%s</p>', $message );

		do_action( 'bigcommerce/settings/render/currency', $section );
	}

	public function render_currency_select( $args ) {
		$channel_term_id = $args['channel'];
		$currencies      = get_option( self::ENABLED_CURRENCIES, [] );
		$default         = get_option( self::CURRENCY_CODE, '' );

		$selected = get_term_meta( $channel_term_id, self::CHANNEL_CURRENCY_CODE, true ) ?: $default;

		if ( ! array_key_exists( $selected, $currencies ) && $selected !== $default ) {
			if ( $args[ 'count' ] > 1 ) {
				$message = sprintf( __( 'The currency <strong>%s</strong> is no longer active for your account. Using <strong>%s</strong> for channel <strong>%s</strong>.', 'bigcommerce' ), $selected, $default, get_term( $channel_term_id, Channel\Channel::NAME )->name );
			} else {
				$message = sprintf( __( 'The currency <strong>%s</strong> is no longer active for your account. Using <strong>%s</strong> instead.', 'bigcommerce' ), $selected, $default );
			}
			printf( '<div class="notice error"><p>%s</p></div>', $message );
		}

		if ( empty( $currencies ) ) {
			$this->render_default_currency_symbol();

			return;
		}

		if ( count( $currencies ) === 1 ) {
			echo esc_html( reset( $currencies )['currency_code'] );

			return;
		}

		printf( '<select name="%1$s[%2$d]" id="field-%1$s-%2$d" class="regular-text bc-field-choices">', esc_attr( self::CHANNEL_CURRENCY_CODE ), $channel_term_id );
		foreach ( $currencies as $currency ) {
			printf( '<option value="%1$s" %2$s>%1$s</option>', esc_attr( $currency['currency_code'] ), selected( $selected, $currency['currency_code'], false ) );
		}
		echo '</select>';

		if ( $args['count'] > 1 ) {
			printf( '<p class="description">%s</p>', esc_html( __( 'Select the currency to use when viewing the store in this channel.', 'bigcommerce' ) ) );
		} else {
			printf( '<p class="description">%s</p>', esc_html( __( 'Select the currency to use for your store.', 'bigcommerce' ) ) );
		}
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

	public function render_default_currency_symbol() {
		$default = get_option( self::CURRENCY_CODE, '' );
		if ( empty( $default ) ) {
			printf( '<p>%s</p>', esc_html( __( 'Currency code will be automatically set when the product import completes', 'bigcommerce' ) ) );
		} else {
			echo esc_html( $default );
		}
	}

	/**
	 * Instead of saving the currency codes for channels as an option,
	 * save as term meta on each channel
	 *
	 * @param array  $new_value
	 *
	 * @return bool
	 * @filter sanitize_option_ . self::CHANNEL_CURRENCY_CODE
	 */
	public function save_channel_currencies( $new_value ) {
		if ( ! is_array( $new_value ) ) {
			return false;
		}
		foreach ( $new_value as $channel_id => $currency_code ) {
			update_term_meta( $channel_id, self::CHANNEL_CURRENCY_CODE, $currency_code );
		}

		return false;
	}

}
