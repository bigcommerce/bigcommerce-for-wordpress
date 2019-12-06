<?php


namespace BigCommerce\Settings\Screens;


use BigCommerce\Container\Settings;

class Connect_Channel_Screen extends Onboarding_Screen {
	const NAME = 'bigcommerce_channel';

	protected function get_page_title() {
		return __( 'Set Up Your Channel', 'bigcommerce' );
	}

	protected function get_menu_title() {
		return __( 'Welcome', 'bigcommerce' );
	}

	protected function get_header() {
		return $this->before_title() . sprintf(
				'<header class="bc-connect__header"><h1 class="bc-settings-connect__title">%s</h1>%s</header>',
				__( 'Set up a channel to finish connecting to BigCommerce.', 'bigcommerce' ),
				$this->get_channels_notices()
			);
	}

	protected function get_channels_notices() {
		return sprintf(
			'<ul class="bc-settings-connect__channels-instructions"><li>%s</li><li>%s</li></ul>',
			__( 'Within BigCommerce, channels are used to define where products are sold and where orders come from. Note that once you save a channel, you cannot change it, as channels cannot be deleted.', 'bigcommerce' ),
			wp_kses( __( 'If you plan on connecting your store to <strong>multiple sites</strong> or you are setting up a <strong>WordPress Multisite</strong>, use your <strong>API Credentials</strong> to connect your store instead. Use the <strong>Start Over</strong> button located at the bottom of this page.', 'bigcommerce' ), [ 'strong' => [] ] )
		);
	}

	protected function submit_button() {
		$this->onboarding_submit_button( 'bc-settings-channel-submit', 'bc-onboarding-arrow', __( 'Continue', 'bigcommerce' ), true );
	}

	public function should_register() {
		return $this->configuration_status === Settings::STATUS_API_CONNECTED;
	}

}
