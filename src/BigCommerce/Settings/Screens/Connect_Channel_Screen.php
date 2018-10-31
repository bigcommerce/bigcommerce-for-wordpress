<?php


namespace BigCommerce\Settings\Screens;


use BigCommerce\Container\Settings;

class Connect_Channel_Screen extends Abstract_Screen {
	const NAME = 'bigcommerce_channel';

	protected function get_page_title() {
		return __( 'Setup Your Channel', 'bigcommerce' );
	}

	protected function get_menu_title() {
		return __( 'Welcome', 'bigcommerce' );
	}

	protected function get_header() {
		$notices_placeholder = '<div class="wp-header-end"></div>'; // placeholder to tell WP where to put notices

		return $notices_placeholder . sprintf(
				'<header class="bc-connect__header"><img src="%s" alt="%s" /><h1 class="bc-settings-connect__title">%s</h1>%s</header>',
				trailingslashit( $this->assets_url ) . 'img/admin/big-commerce-logo.svg',
				__( 'BigCommerce', 'bigcommerce' ),
				__( 'Setup your channel to finish connecting to BigCommerce.', 'bigcommerce' ),
				$this->get_channels_notices()
			);
	}

	protected function get_channels_notices() {
		return sprintf(
			'<ul class="bc-settings-connect__channels-instructions"><li>%s</li><li>%s</li></ul>',
			__( 'Once you save your channel, you cannot change this option.', 'bigcommerce' ),
			__( 'Channels cannot be deleted.', 'bigcommerce' )
		);
	}

	protected function submit_button() {
		submit_button( __( 'Set Channel', 'bigcommerce' ), 'bc-admin-btn', 'submit', true, ['data-js' => 'bc-settings-channel-submit', 'disabled' => 'disabled'] );
	}

	public function should_register() {
		return $this->configuration_status === Settings::STATUS_API_CONNECTED;
	}

}
