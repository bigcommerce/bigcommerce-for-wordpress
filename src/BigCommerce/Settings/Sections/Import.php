<?php


namespace BigCommerce\Settings\Sections;


use BigCommerce\GraphQL\BaseGQL;
use BigCommerce\Import\Image_Importer;
use BigCommerce\Settings\Screens\Connect_Channel_Screen;
use BigCommerce\Settings\Screens\Settings_Screen;

class Import extends Settings_Section {

	use Webhooks, ImportType, Images;

	const NAME                     = 'import';
	const OPTION_FREQUENCY         = 'bigcommerce_import_frequency';
	const OPTION_NEW_PRODUCTS      = 'bigcommerce_import_new_products';
	const BATCH_SIZE               = 'bigcommerce_import_batch_size';
	const ENABLE_PRODUCTS_WEBHOOKS = 'bigcommerce_import_enable_webhooks';
	const ENABLE_CUSTOMER_WEBHOOKS = 'bigcommerce_import_enable_customer_webhooks';
	const ENABLE_IMAGE_IMPORT      = 'bigcommerce_import_enable_image_import';
	const MAX_CONCURRENT           = 'bigcommerce_import_max_concurrent';
	const RUN_IN_PARALLEL          = 'bigcommerce_parallel_run';
	const HEADLESS_FLAG            = 'bigcommerce_headless_flag';

	const FREQUENCY_FIVE    = 'five_minutes';
	const FREQUENCY_THIRTY  = 'thirty_minutes';
	const FREQUENCY_HOURLY  = 'hourly';
	const FREQUENCY_DAILY   = 'daily';
	const FREQUENCY_WEEKLY  = 'weekly';
	const FREQUENCY_MONTHLY = 'monthly';
	const FREQUENCY_NEVER   = 'never';

	const DEFAULT_FREQUENCY = self::FREQUENCY_FIVE;
	const PRODUCT_TRANSIENT = 'bigcommerce_products_transient_interval';

	protected $options;
	protected $product_transient_groups;

	public function __construct() {
		$this->options = [
			0 => __( 'Full - Import and store all product data in WP database (default)', 'bigcommerce' ),
			1 => __( 'Fast - Headless - Import and store minimal product data (beta)', 'bigcommerce' ),
		];

		$this->product_transient_groups = [
			MINUTE_IN_SECONDS      => __( '1 minute', 'bigcommerce' ),
			5 * MINUTE_IN_SECONDS  => __( '5 minutes', 'bigcommerce' ),
			15 * MINUTE_IN_SECONDS => __( '15 minutes', 'bigcommerce' ),
			HOUR_IN_SECONDS        => __( '1 hour', 'bigcommerce' ),
			3 * HOUR_IN_SECONDS    => __( '3 hours', 'bigcommerce' ),
			12 * HOUR_IN_SECONDS   => __( '12 hours(default)', 'bigcommerce' ),
			DAY_IN_SECONDS         => __( '24 hours', 'bigcommerce' ),
		];
	}

	/**
	 * @return void
	 * @action bigcommerce/settings/register/screen= . Settings_Screen::NAME
	 */
	public function register_settings_section() {
		add_settings_field(
			self::HEADLESS_FLAG,
			__( 'Product Import', 'bigcommerce' ),
			[ $this, 'render_headless_flag_import' ],
			Settings_Screen::NAME,
			self::NAME
		);
		register_setting( Settings_Screen::NAME, self::HEADLESS_FLAG );

		add_settings_field(
			self::PRODUCT_TRANSIENT,
			esc_html( __( 'Products Cache Expiration', 'bigcommerce' ) ),
			[ $this, 'render_products_transient_settings' ],
			Settings_Screen::NAME,
			self::NAME,
			[
				'option' => self::PRODUCT_TRANSIENT,
				'label'  => __( 'Expires after', 'bigcommerce' ),
			]
		);

		register_setting( Settings_Screen::NAME, self::PRODUCT_TRANSIENT );

		register_setting( Settings_Screen::NAME, BaseGQL::TOKEN_EXPIRATION );

		add_settings_field(
			BaseGQL::TOKEN_EXPIRATION,
			esc_html( __( 'Storefront token expiration time', 'bigcommerce' ) ),
			[ $this, 'render_gql_token_transient_settings' ],
			Settings_Screen::NAME,
			self::NAME,
			[
				'option' => BaseGQL::TOKEN_EXPIRATION,
				'label'  => __( 'Expires after', 'bigcommerce' ),
			],
		);

		add_settings_section(
			self::NAME,
			__( 'Product Sync', 'bigcommerce' ),
			false,
			Settings_Screen::NAME
		);


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
			self::ENABLE_IMAGE_IMPORT,
			__( 'Images import', 'bigcommerce' ),
			[ $this, 'enable_images_import_toggle' ],
			Settings_Screen::NAME,
			self::NAME,
			[
				'type'   => 'radio',
				'option' => self::ENABLE_IMAGE_IMPORT,
				'label'  => __( 'Allow product images import', 'bigcommerce' ),
			]
		);

		register_setting( Settings_Screen::NAME, self::ENABLE_IMAGE_IMPORT );

		add_settings_field(
			self::RUN_IN_PARALLEL,
			__( 'Import Tasks Processing', 'bigcommerce' ),
			[ $this, 'render_import_tasks_processing' ],
			Settings_Screen::NAME,
			self::NAME
		);

		register_setting( Settings_Screen::NAME, self::RUN_IN_PARALLEL );

		add_settings_field(
			self::ENABLE_PRODUCTS_WEBHOOKS,
			__( 'Enable Products Webhooks', 'bigcommerce' ),
			[ $this, 'enable_products_webhooks_toggle' ],
			Settings_Screen::NAME,
			self::NAME
		);

		register_setting( Settings_Screen::NAME, self::ENABLE_PRODUCTS_WEBHOOKS );

		add_settings_field(
			self::ENABLE_CUSTOMER_WEBHOOKS,
			__( 'Enable Customers Webhooks', 'bigcommerce' ),
			[ $this, 'enable_customer_webhooks_toggle' ],
			Settings_Screen::NAME,
			self::NAME
		);

		register_setting( Settings_Screen::NAME, self::ENABLE_CUSTOMER_WEBHOOKS );

		add_settings_field(
			self::HEADLESS_FLAG,
			__( 'Product Import', 'bigcommerce' ),
			[ $this, 'render_headless_flag_import' ],
			Settings_Screen::NAME,
			self::NAME
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
			self::FREQUENCY_NEVER   => __( 'Never', 'bigcommerce' ),
		];
		$this->section_description();
		$select = sprintf( '<select id="field-%s" name="%s" class="regular-text bc-field-choices">', esc_attr( self::OPTION_FREQUENCY ), esc_attr( self::OPTION_FREQUENCY ) );
		foreach ( $options as $key => $label ) {
			$select .= sprintf( '<option value="%s" %s>%s</option>', esc_attr( $key ), selected( $current, $key, false ), esc_html( $label ) );
		}
		$select .= '</select>';

		echo $select;
	}

	/**
	 * Render GQL token expiration options field
	 *
	 * @param $args
	 */
	public function render_gql_token_transient_settings( $args ) {
		printf( '<p class="description"><strong>%s</strong> <br /> %s</p>', esc_html( __( 'For Advanced Users only', 'bigcommerce' ) ), esc_html( __( 'Set expiration time for storefront tokens - how often tokens will be rotated. It does not affect product information retrieve. It is for security purposes only', 'bigcommerce' ) ) );
		$value = get_option( $args['option'], 12 * HOUR_IN_SECONDS );
		printf( '<select id="field-%s" name="%s" data-js="bc-dynamic-state-control" class="regular-text bc-field-choices bc-settings-headless">', esc_attr( BaseGQL::TOKEN_EXPIRATION ), esc_attr( BaseGQL::TOKEN_EXPIRATION ) );
		foreach ( $this->product_transient_groups as $time => $label ) {
			printf( '<option value="%d" %s>%s</option>', $time, selected( $value, $time, false ), esc_html( $label ) );
		}
		printf( '</select>' );
	}

	public function render_products_transient_settings( $args ) {
		$value = get_option( $args['option'], 15 * MINUTE_IN_SECONDS );
		printf( '<select id="field-%s" name="%s" data-js="bc-dynamic-state-control" class="regular-text bc-field-choices bc-settings-headless">', esc_attr( self::PRODUCT_TRANSIENT ), esc_attr( self::PRODUCT_TRANSIENT ) );
		foreach ( $this->product_transient_groups as $time => $label ) {
			printf( '<option value="%d" %s>%s</option>', $time, selected( $value, $time, false ), esc_html( $label ) );
		}
		printf( '</select>' );
	}

	public function new_products_toggle() {
		$current = get_option( self::OPTION_NEW_PRODUCTS, 1 );

		printf( '<p class="description">%s</p>', esc_html( __( 'Would you like the listings in your channel automatically populated?', 'bigcommerce' ) ) );
		echo '<fieldset class="bc-settings-traditional">';
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

	public function render_import_tasks_processing() {
		$current = get_option( Import::RUN_IN_PARALLEL, 0 );

		echo '<fieldset class="bc-settings-traditional">';
		$link            = 'https://developer.bigcommerce.com/bigcommerce-for-wordpress/ZG9jOjQ1MjA3MTQ2-creating-reliable-cron-jobs';
		$learn_more_link = sprintf( '<a href="%s" target="_blank">%s</a>', $link, esc_html( __( 'Learn More', 'bigcommerce' ) ) );

		echo '<p class="description">';
		printf( esc_html( __( 'The BigCommerce for WordPress plugin relies on WP-Cron for background tasks to update/sync data from BigCommerce. Configuring a server-side cron can greatly increase performance. %s', 'bigcommerce' ) ), $learn_more_link );
		echo '</p>';
		printf(
			'<p><label><input type="radio" name="%s" value="0" %s /> %s</label></p>',
			esc_attr( Import::RUN_IN_PARALLEL ),
			checked( 0, (int) $current, false ),
			esc_html( __( 'Classic import processing. Single process for running import tasks', 'bigcommerce' ) )
		);
		printf(
			'<p><label><input type="radio" name="%s" value="1" %s /> %s</label></p>',
			esc_attr( Import::RUN_IN_PARALLEL ),
			checked( 1, (int) $current, false ),
			esc_html( __( "Import processing in parallel for fetching listings, products, channel initialization", 'bigcommerce' ) )
		);
		echo '</fieldset>';
	}

}
