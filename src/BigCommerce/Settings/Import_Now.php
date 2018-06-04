<?php


namespace BigCommerce\Settings;


use BigCommerce\Api_Factory;
use BigCommerce\Import\Runner\Cron_Runner;

class Import_Now {
	const ACTION = 'bigcommerce_import_now';

	private $factory;

	/** @var Settings_Screen */
	private $settings_screen;

	/**
	 * Import_Now constructor.
	 *
	 * @param Api_Factory     $api_factory
	 * @param Settings_Screen $settings_screen
	 */
	public function __construct( Api_Factory $api_factory, Settings_Screen $settings_screen ) {
		$this->factory         = $api_factory;
		$this->settings_screen = $settings_screen;
	}

	/**
	 * @return void
	 * @action bigcommerce/settings/render/frequency
	 */
	public function render_button() {
		$button = '<a href="%s" class="button secondary">%s</a>';
		$button = sprintf( $button, esc_url( $this->get_import_url() ), __( 'Import Now', 'bigcommerce' ) );
		printf( '<p>%s</p>', $button );
	}

	/**
	 * @return string
	 */
	public function get_import_url() {
		$url = admin_url( 'admin-post.php' );
		$url = add_query_arg( [
			'action' => self::ACTION,
		], $url );
		$url = wp_nonce_url( $url, self::ACTION );

		return $url;
	}

	/**
	 * @return void
	 * @action admin_post_ . self::ACTION
	 */
	public function handle_request() {
		check_admin_referer( self::ACTION );

		do_action( Cron_Runner::START_CRON );

		wp_safe_redirect( esc_url_raw( $this->settings_screen->get_url() ) );
		exit();
	}
}