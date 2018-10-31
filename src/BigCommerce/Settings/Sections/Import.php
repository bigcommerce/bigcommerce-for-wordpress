<?php


namespace BigCommerce\Settings\Sections;


use BigCommerce\Settings\Screens\Settings_Screen;

class Import extends Settings_Section {
	const NAME                     = 'import';
	const OPTION_FREQUENCY         = 'bigcommerce_import_frequency';

	const FREQUENCY_FIVE    = 'five_minutes';
	const FREQUENCY_THIRTY  = 'thirty_minutes';
	const FREQUENCY_HOURLY  = 'hourly';
	const FREQUENCY_DAILY   = 'daily';
	const FREQUENCY_WEEKLY  = 'weekly';
	const FREQUENCY_MONTHLY = 'monthly';

	const DEFAULT_FREQUENCY = self::FREQUENCY_FIVE;

	/**
	 * @return void
	 * @action bigcommerce/settings/register/screen= . Settings_Screen::NAME
	 */
	public function register_settings_section() {
		add_settings_section(
			self::NAME,
			__( 'Product Sync', 'bigcommerce' ),
			false,
			Settings_Screen::NAME
		);

		add_action( 'bigcommerce/settings/section/before_callback/id=' . self::NAME, [ $this, 'section_description' ], 10, 0 );

		add_settings_field(
			self::OPTION_FREQUENCY,
			__( 'Sync Frequency', 'bigcommerce' ),
			[ $this, 'frequency_select' ],
			Settings_Screen::NAME,
			self::NAME
		);

		register_setting(
			Settings_Screen::NAME,
			self::OPTION_FREQUENCY
		);
	}

	public function section_description() {
		printf( '<p class="description">%s</p>', esc_html__( 'We will check for new products and updates to existing products and import them for you automatically.', 'bigcommerce' ) );
	}

	/**
	 * @return string
	 */
	public function frequency_select() {
		$current = get_option( self::OPTION_FREQUENCY, self::DEFAULT_FREQUENCY );
		$options = [
			self::FREQUENCY_FIVE    => __( 'Five Minutes', 'bigcommerce' ),
			self::FREQUENCY_THIRTY  => __( 'Thirty Minutes', 'bigcommerce' ),
			self::FREQUENCY_HOURLY  => __( 'Hour', 'bigcommerce' ),
			self::FREQUENCY_DAILY   => __( 'Day', 'bigcommerce' ),
			self::FREQUENCY_WEEKLY  => __( 'Week', 'bigcommerce' ),
			self::FREQUENCY_MONTHLY => __( 'Month', 'bigcommerce' ),
		];

		$select = sprintf( '<select name="%s" class="regular-text bc-field-choices">', esc_attr( self::OPTION_FREQUENCY ) );
		foreach ( $options as $key => $label ) {
			$select .= sprintf( '<option value="%s" %s>%s</option>', esc_attr( $key ), selected( $current, $key, false ), esc_html( $label ) );
		}
		$select .= '</select>';

		echo $select;
	}
}
