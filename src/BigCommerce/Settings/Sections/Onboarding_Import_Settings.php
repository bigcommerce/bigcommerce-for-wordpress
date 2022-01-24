<?php

namespace BigCommerce\Settings\Sections;

use BigCommerce\Settings\Screens\Connect_Channel_Screen;

class Onboarding_Import_Settings extends Settings_Section {

	use Webhooks;

	const NAME = 'import_settings';

	public function register_settings_section() {
		add_settings_section(
			self::NAME,
			__( 'Import Settings', 'bigcommerce' ),
			'__return_false',
			Connect_Channel_Screen::NAME
		);

		add_settings_field(
			Import::ENABLE_PRODUCTS_WEBHOOKS,
			__( 'Enable Products Webhooks', 'bigcommerce' ),
			[ $this, 'enable_products_webhooks_toggle' ],
			Connect_Channel_Screen::NAME,
			self::NAME
		);

		register_setting( Connect_Channel_Screen::NAME, Import::ENABLE_PRODUCTS_WEBHOOKS );

		add_settings_field(
			Import::ENABLE_CUSTOMER_WEBHOOKS,
			__( 'Enable Customers Webhooks', 'bigcommerce' ),
			[ $this, 'enable_customer_webhooks_toggle' ],
			Connect_Channel_Screen::NAME,
			self::NAME
		);

		register_setting( Connect_Channel_Screen::NAME, Import::ENABLE_CUSTOMER_WEBHOOKS );
	}
}
