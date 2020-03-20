<?php

namespace BigCommerce\Settings\Sections;


use BigCommerce\Settings\Screens\Connect_Channel_Screen;

class Onboarding_Import_Settings extends Settings_Section {
	use Webhooks;

	const NAME         = 'import_settings';

	public function register_settings_section() {
		add_settings_section(
			self::NAME,
			__( 'Import Settings', 'bigcommerce' ),
			'__return_false',
			Connect_Channel_Screen::NAME
		);

		add_settings_field(
			Import::ENABLE_WEBHOOKS,
			__( 'Enable Webhooks', 'bigcommerce' ),
			[ $this, 'enable_webhooks_toggle' ],
			Connect_Channel_Screen::NAME,
			self::NAME
		);

		register_setting(
			Connect_Channel_Screen::NAME,
			Import::ENABLE_WEBHOOKS
		);
	}
}