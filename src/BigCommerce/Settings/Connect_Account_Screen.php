<?php


namespace BigCommerce\Settings;


class Connect_Account_Screen extends Abstract_Screen {
	const NAME = 'bigcommerce_connect';

	protected function get_page_title() {
		return __( 'Connect My Account', 'bigcommerce' );
	}

	protected function get_menu_title() {
		return __( 'Connect Account', 'bigcommerce' );
	}

	protected function get_header() {
		$notices_placeholder = '<div class="wp-header-end"></div>'; // placeholder to tell WP where to put notices
		return $notices_placeholder . sprintf( '<header class="bc-connect__header"><img src="%s" alt="%s" /><h1 class="bc-settings-connect__title">%s</h1></header>', trailingslashit( $this->assets_url ) . 'img/admin/big-commerce-logo.svg', __( 'BigCommerce', 'bigcommerce' ), __( 'We just need a few details to connect to your store.', 'bigcommerce' ) );
	}

	protected function parent_slug() {
		return null;
	}

	protected function submit_button() {
		submit_button( __( 'Connect My Account', 'bigcommerce' ) );
	}

	protected function should_register() {
		return ! $this->plugin_configured;
	}

}
