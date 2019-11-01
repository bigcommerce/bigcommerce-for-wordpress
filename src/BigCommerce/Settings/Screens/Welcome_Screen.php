<?php


namespace BigCommerce\Settings\Screens;


use BigCommerce\Container\Settings;
use BigCommerce\Settings\Onboarding_Videos;

class Welcome_Screen extends Onboarding_Screen {
	const NAME      = 'bigcommerce_welcome';

	/** @var string Path to the templates/admin directory */
	private $template_dir;

	public function __construct( $configuration_status, $assets_url, $template_dir ) {
		parent::__construct( $configuration_status, $assets_url );
		$this->template_dir = trailingslashit( $template_dir );
	}

	protected function get_page_title() {
		return __( 'BigCommerce for WordPress', 'bigcommerce' );
	}

	protected function get_menu_title() {
		return __( 'Welcome', 'bigcommerce' );
	}

	public function render_settings_page() {
		$_GET[ 'settings-updated' ] = 1;
		settings_errors();
		unset( $_GET[ 'settings-updated' ] );
		$connect_account_url = $this->get_connect_account_url();
		$create_account_url  = $this->get_create_account_url();
		$credentials_url     = $this->get_credentials_url();
		$notices             = $this->get_notices();
		$video               = $this->get_video();
		include trailingslashit( $this->template_dir ) . 'welcome-screen.php';
	}

	private function get_connect_account_url() {
		return apply_filters( 'bigcommerce/settings/connect_account_url', 'https://www.bigcommerce.com/' );
	}

	private function get_create_account_url() {
		return apply_filters( 'bigcommerce/settings/create_account_url', 'https://www.bigcommerce.com/' );
	}

	private function get_credentials_url() {
		return apply_filters( 'bigcommerce/settings/credentials_url', admin_url() );
	}

	public function should_register() {
		return $this->configuration_status === Settings::STATUS_NEW;
	}

	private function get_notices() {
		/**
		 * Filter the array of notices and promotions displayed on the plugin
		 * welcome screen. The expected data is an array of arrays, each
		 * with a 'title' and 'content' key. The values of those keys
		 * should be HTML-safe strings.
		 *
		 * The 'title' will be output inside an h3 tag. The 'content' will
		 * be output inside a div tag.
		 *
		 * @param array $notices
		 */
		$notices = apply_filters( 'bigcommerce/settings/welcome/notices', [] );

		return (array) $notices;
	}

	private function get_video() {
		return $this->make_video_embed( Onboarding_Videos::OVERVIEW );
	}

}
