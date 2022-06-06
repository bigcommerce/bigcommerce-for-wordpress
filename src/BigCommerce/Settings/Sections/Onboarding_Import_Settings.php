<?php

namespace BigCommerce\Settings\Sections;

use BigCommerce\Settings\Screens\Connect_Channel_Screen;
use BigCommerce\Settings\Screens\Settings_Screen;

class Onboarding_Import_Settings extends Settings_Section {

	use Webhooks, ImportType, Images;

	const NAME = 'import_settings';

	protected $options;

	public function __construct() {
		$this->options = [
			0 => __( 'Full - Import and store all product data in WP database (default)', 'bigcommerce' ),
			1 => __( 'Fast - Headless - Import and store minimal product data (beta)', 'bigcommerce' ),
		];
	}

	public function register_settings_section() {
		add_settings_section(
			self::NAME,
			__( 'Import Settings', 'bigcommerce' ),
			'__return_false',
			Connect_Channel_Screen::NAME
		);

		register_setting( Connect_Channel_Screen::NAME, Import::ENABLE_CUSTOMER_WEBHOOKS );

		add_settings_field(
				Import::HEADLESS_FLAG,
				__( 'Enable traditional Product Import?', 'bigcommerce' ),
				[ $this, 'render_headless_flag_import' ],
				Connect_Channel_Screen::NAME,
				self::NAME
		);

		add_settings_field(
			Import::ENABLE_PRODUCTS_WEBHOOKS,
			__( 'Enable Products Webhooks', 'bigcommerce' ),
			[ $this, 'enable_products_webhooks_toggle' ],
			Connect_Channel_Screen::NAME,
			self::NAME
		);

		add_settings_field(
			Import::ENABLE_IMAGE_IMPORT,
			__( 'Images import', 'bigcommerce' ),
			[ $this, 'enable_images_import_toggle' ],
			Connect_Channel_Screen::NAME,
			self::NAME,
			[
				'type'   => 'radio',
				'option' => Import::ENABLE_IMAGE_IMPORT,
				'label'  => __( 'Allow product images import', 'bigcommerce' ),
			],
		);

		register_setting( Connect_Channel_Screen::NAME, Import::ENABLE_PRODUCTS_WEBHOOKS );

		add_settings_field(
			Import::ENABLE_CUSTOMER_WEBHOOKS,
			__( 'Enable Customers Webhooks', 'bigcommerce' ),
			[ $this, 'enable_customer_webhooks_toggle' ],
			Connect_Channel_Screen::NAME,
			self::NAME
		);

		register_setting( Connect_Channel_Screen::NAME, Import::ENABLE_IMAGE_IMPORT );


		register_setting( Connect_Channel_Screen::NAME, Import::HEADLESS_FLAG );
	}
}
