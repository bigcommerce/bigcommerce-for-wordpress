<?php


namespace BigCommerce\Settings\Sections;


use BigCommerce\Settings\Screens\Connect_Channel_Screen;
use BigCommerce\Settings\Screens\Settings_Screen;

class Import extends Settings_Section {
	use Webhooks;

	const NAME                = 'import';
	const OPTION_FREQUENCY    = 'bigcommerce_import_frequency';
	const OPTION_NEW_PRODUCTS = 'bigcommerce_import_new_products';
	const BATCH_SIZE          = 'bigcommerce_import_batch_size';
	const ENABLE_WEBHOOKS     = 'bigcommerce_import_enable_webhooks';
	const MAX_CONCURRENT      = 'bigcommerce_import_max_concurrent';

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

		add_action( 'bigcommerce/settings/section/before_callback/id=' . self::NAME, [
			$this,
			'section_description',
		], 10, 0 );

		add_settings_field(
			self::OPTION_FREQUENCY,
			__( 'Sync Frequency', 'bigcommerce' ),
			[ $this, 'frequency_select' ],
			Settings_Screen::NAME,
			self::NAME,
			[
				'label_for' => 'field-' . self::OPTION_FREQUENCY,
			]
		);

		register_setting(
			Settings_Screen::NAME,
			self::OPTION_FREQUENCY
		);

		add_settings_field(
			self::OPTION_NEW_PRODUCTS,
			__( 'Automatic Listing', 'bigcommerce' ),
			[ $this, 'new_products_toggle' ],
			Settings_Screen::NAME,
			self::NAME
		);

		register_setting(
			Settings_Screen::NAME,
			self::OPTION_NEW_PRODUCTS
		);


		add_settings_field(
			self::BATCH_SIZE,
			__( 'Import Batch Size', 'bigcommerce' ),
			[ $this, 'render_number_field' ],
			Settings_Screen::NAME,
			self::NAME,
			[
				'min'         => 1,
				'max'         => 25,
				'option'      => self::BATCH_SIZE,
				'default'     => 5,
				'description' => __( 'How many products to import in each batch. If your import is hitting system limits, consider lowering this number.', 'bigcommerce' ),
				'label_for' => 'field-' . self::BATCH_SIZE,
			]
		);

		register_setting(
			Settings_Screen::NAME,
			self::BATCH_SIZE
		);

		add_settings_field(
			self::ENABLE_WEBHOOKS,
			__( 'Enable Webhooks', 'bigcommerce' ),
			[ $this, 'enable_webhooks_toggle' ],
			Settings_Screen::NAME,
			self::NAME
		);

		register_setting(
			Settings_Screen::NAME,
			self::ENABLE_WEBHOOKS
		);

		/* // disabled until we figure out how to implement concurrent processing
		add_settings_field(
			self::MAX_CONCURRENT,
			__( 'Max Concurrent Imports', 'bigcommerce' ),
			[ $this, 'render_number_field' ],
			Settings_Screen::NAME,
			self::NAME,
			[
				'min'         => 1,
				'max'         => 25,
				'option'      => self::MAX_CONCURRENT,
				'default'     => 1,
				'description' => __( 'How many concurrent import batches to run in separate processes. If you import is hitting system limits or you find site performance lagging while imports run, consider lowering this number.', 'bigcommerce' ),
			]
		);

		register_setting(
			Settings_Screen::NAME,
			self::MAX_CONCURRENT
		);*/
	}

	/**
	 * We want just the automatic list setting to show up in the channel
	 * selection screen during onboarding, to give the merchant a chance
	 * to change it before the first import.
	 *
	 * @return void
	 * @action bigcommerce/settings/register/screen= . Connect_Channel_Screen::NAME
	 */
	public function register_connect_channel_fields() {
		add_settings_field(
			self::OPTION_NEW_PRODUCTS,
			__( 'Automatic Listing', 'bigcommerce' ),
			[ $this, 'new_products_toggle' ],
			Connect_Channel_Screen::NAME,
			Channel_Select::NAME
		);

		register_setting(
			Connect_Channel_Screen::NAME,
			self::OPTION_NEW_PRODUCTS
		);
	}

	public function section_description() {
		printf( '<p class="description">%s</p>', esc_html__( 'We will check for new products and updates to existing products and import them for you automatically.', 'bigcommerce' ) );
	}

	/**
	 * @return void
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

		$select = sprintf( '<select id="field-%s" name="%s" class="regular-text bc-field-choices">', esc_attr( self::OPTION_FREQUENCY ), esc_attr( self::OPTION_FREQUENCY ) );
		foreach ( $options as $key => $label ) {
			$select .= sprintf( '<option value="%s" %s>%s</option>', esc_attr( $key ), selected( $current, $key, false ), esc_html( $label ) );
		}
		$select .= '</select>';

		echo $select;
	}

	public function new_products_toggle() {
		$current = get_option( self::OPTION_NEW_PRODUCTS, 1 );

		printf( '<p class="description">%s</p>', esc_html( __( 'Would you like the listings in your channel automatically populated?', 'bigcommerce' ) ) );

		echo '<fieldset>';
		printf(
			'<p><label><input type="radio" name="%s" value="1" %s /> %s</label></p>',
			esc_attr( self::OPTION_NEW_PRODUCTS ),
			checked( 1, (int) $current, false ),
			esc_html( __( 'Yes, automatically list new BigCommerce products on this Channel', 'bigcommerce' ) )
		);
		printf(
			'<p><label><input type="radio" name="%s" value="0" %s /> %s</label></p>',
			esc_attr( self::OPTION_NEW_PRODUCTS ),
			checked( 0, (int) $current, false ),
			esc_html( __( "No, I'll select which products should be listed on this Channel within BigCommerce", 'bigcommerce' ) )
		);
		echo '</fieldset>';
	}
}
