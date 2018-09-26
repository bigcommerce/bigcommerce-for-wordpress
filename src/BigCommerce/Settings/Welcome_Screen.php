<?php


namespace BigCommerce\Settings;


class Welcome_Screen extends Abstract_Screen {
	const NAME = 'bigcommerce_welcome';

	/** @var string Path to the admin-views directory */
	private $template_dir;

	private $connect_screen;

	public function __construct( $plugin_configured, $assets_url, $template_dir, Connect_Account_Screen $connect_account_screen ) {
		parent::__construct( $plugin_configured, $assets_url );
		$this->template_dir   = trailingslashit( $template_dir );
		$this->connect_screen = $connect_account_screen;
	}

	protected function get_page_title() {
		return __( 'BigCommerce for WordPress', 'bigcommerce' );
	}

	protected function get_menu_title() {
		return __( 'Welcome', 'bigcommerce' );
	}

	public function render_settings_page() {
		$connect_account_url = $this->get_connect_account_url();
		$create_account_url  = $this->get_create_account_url();
		$notices = $this->get_notices();
		include trailingslashit( $this->template_dir ) . 'welcome-screen.php';
	}

	private function get_connect_account_url() {
		return $this->connect_screen->get_url();
	}

	private function get_create_account_url() {
		return 'https://www.bigcommerce.com/';
	}

	protected function should_register() {
		return ! $this->plugin_configured;
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
		$notices = apply_filters( 'bigcomerce/settings/welcome/notices', [] );
		return (array) $notices;
	}
}