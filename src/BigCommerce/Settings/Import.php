<?php


namespace BigCommerce\Settings;


class Import {
	const NAME                     = 'import';
	const OPTION_FREQUENCY         = 'bigcommerce_import_frequency';
	const OPTION_DISABLE_OVERWRITE = 'bigcommerce_import_overwrite';

	const FREQUENCY_FIVE    = 'five_minutes';
	const FREQUENCY_THIRTY  = 'thirty_minutes';
	const FREQUENCY_HOURLY  = 'hourly';
	const FREQUENCY_DAILY   = 'daily';
	const FREQUENCY_WEEKLY  = 'weekly';
	const FREQUENCY_MONTHLY = 'monthly';

	const DEFAULT_FREQUENCY = self::FREQUENCY_FIVE;

	/**
	 * @return void
	 * @action bigcommerce/settings/register
	 */
	public function register_settings_section() {
		add_settings_section(
			self::NAME,
			__( 'Product Sync', 'bigcommerce' ),
			[ $this, 'render_section' ],
			Settings_Screen::NAME
		);

		register_setting(
			Settings_Screen::NAME,
			self::OPTION_FREQUENCY
		);

//		add_settings_field(
//			self::OPTION_DISABLE_OVERWRITE,
//			__( 'Do not update products on import', 'bigcommerce' ),
//			[ $this, 'render_overwrite_checkbox' ],
//			Settings_Screen::NAME,
//			self::NAME
//		);

		register_setting(
			Settings_Screen::NAME,
			self::OPTION_DISABLE_OVERWRITE
		);
	}

	/**
	 * @param array $section
	 *
	 * @return void
	 */
	public function render_section( $section ) {
		printf( '<p>%s</p>', esc_html__( 'We will check for new products and updates to existing products and import them for you automatically every:', 'bigcommerce' ) );
		echo $this->frequency_select();
		do_action( 'bigcommerce/settings/render/frequency', $section );
	}

	/**
	 * @return string
	 */
	private function frequency_select() {
		$current = get_option( self::OPTION_FREQUENCY, self::DEFAULT_FREQUENCY );
		$options = [
			self::FREQUENCY_FIVE    => __( 'Five Minutes', 'bigcommerce' ),
			self::FREQUENCY_THIRTY  => __( 'Thirty Minutes', 'bigcommerce' ),
			self::FREQUENCY_HOURLY  => __( 'Hour', 'bigcommerce' ),
			self::FREQUENCY_DAILY   => __( 'Day', 'bigcommerce' ),
			self::FREQUENCY_WEEKLY  => __( 'Week', 'bigcommerce' ),
			self::FREQUENCY_MONTHLY => __( 'Month', 'bigcommerce' ),
		];

		$select = sprintf( '<select name="%s" class="regular-text">', esc_attr( self::OPTION_FREQUENCY ) );
		foreach ( $options as $key => $label ) {
			$select .= sprintf( '<option value="%s" %s>%s</option>', esc_attr( $key ), selected( $current, $key, false ), esc_html( $label ) );
		}
		$select .= '</select>';

		return $select;
	}

	public function render_overwrite_checkbox() {
		$value = get_option( self::OPTION_DISABLE_OVERWRITE, 1 );
		$description = __( 'If enabled, any content changes you make to products in WordPress will be retained on the next scheduled import. Note that you can override this setting for specific products in the post editor.', 'bigcommerce' );
		printf( '<p class="description"><input type="checkbox" name="%s" value="1" %s /> %s</p>', esc_attr( self::OPTION_DISABLE_OVERWRITE ), checked( 1, $value, false ), $description );
	}
}