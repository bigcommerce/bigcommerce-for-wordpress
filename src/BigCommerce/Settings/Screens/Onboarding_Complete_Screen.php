<?php


namespace BigCommerce\Settings\Screens;

use BigCommerce\Container\Settings;
use BigCommerce\Merchant\Onboarding_Api;
use BigCommerce\Merchant\Setup_Status;

class Onboarding_Complete_Screen extends Onboarding_Screen {
	const NAME = 'bigcommerce_launch_steps';

	/** @var string Path to the templates/admin directory */
	private $template_dir;

	/**
	 * @var Setup_Status
	 */
	private $status;

	public function __construct( $configuration_status, $assets_url, $template_dir, Setup_Status $status ) {
		parent::__construct( $configuration_status, $assets_url );
		$this->template_dir = trailingslashit( $template_dir );
		$this->status       = $status;
	}

	protected function get_page_title() {
		return __( "That's it. You're done!", 'bigcommerce' );
	}

	protected function get_menu_title() {
		return __( 'Launch Steps', 'bigcommerce' );
	}

	protected function parent_slug() {
		$required = $this->status->get_required_steps();
		if ( empty( $required ) ) {
			return null;
		}

		return parent::parent_slug();
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

		ob_start();
		$this->before_form();
		$this->start_form();
		$this->settings_fields();
		$this->do_settings_sections( static::NAME );
		$this->submit_button();
		$this->end_form();
		$this->after_form();
		$settings_sections = ob_get_clean();

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
