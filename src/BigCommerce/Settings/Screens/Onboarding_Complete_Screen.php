<?php


namespace BigCommerce\Settings\Screens;

use BigCommerce\Container\Settings;
use BigCommerce\Merchant\Onboarding_Api;

class Onboarding_Complete_Screen extends Onboarding_Screen {
	const NAME = 'bigcommerce_setup_complete';

	/** @var string Path to the templates/admin directory */
	private $template_dir;

	public function __construct( $configuration_status, $assets_url, $template_dir ) {
		parent::__construct( $configuration_status, $assets_url );
		$this->template_dir = trailingslashit( $template_dir );
	}

	protected function get_page_title() {
		return __( "That's it. You're done!", 'bigcommerce' );
	}

	protected function get_menu_title() {
		return __( 'Welcome', 'bigcommerce' );
	}

	protected function parent_slug() {
		return null;
	}

	public function render_settings_page() {
		$_GET['settings-updated'] = 1;
		settings_errors();
		unset( $_GET['settings-updated'] );
		$new_account   = $this->new_account();
		$settings_url  = $this->settings_url();
		$create_url    = $this->create_url();
		$support_url   = $this->support_url();
		$extend_url    = $this->extend_url();
		$customize_url = $this->customize_url();
		include trailingslashit( $this->template_dir ) . 'complete-screen.php';
	}

	protected function submit_button() {

	}

	public function should_register() {
		return $this->configuration_status === Settings::STATUS_COMPLETE;
	}

	private function new_account() {
		$account_id = get_option( Onboarding_Api::ACCOUNT_ID, '' );

		return ! empty( $account_id );
	}

	private function settings_url() {
		return apply_filters( 'bigcommerce/settings/settings_url', admin_url() );
	}

	private function create_url() {
		return 'https://login.bigcommerce.com/deep-links/manage/products';
	}

	private function support_url() {
		return apply_filters( 'bigcommerce/settings/resources_url', admin_url() );
	}

	private function extend_url() {
		return apply_filters( 'bigcommerce/documentation/url', 'https://developer.bigcommerce.com/bigcommerce-for-wordpress/' );
	}

	private function customize_url() {
		return add_query_arg( [
			'autofocus[panel]' => \BigCommerce\Customizer\Panels\Primary::NAME,
		], admin_url( 'customize.php' ) );
	}

}