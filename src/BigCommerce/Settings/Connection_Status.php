<?php


namespace BigCommerce\Settings;


use BigCommerce\Api\ConfigurationRequiredException;
use BigCommerce\Api\v3\ApiException;
use BigCommerce\Api\v3\Api\CatalogApi;
use BigCommerce\Container\Settings;
use BigCommerce\Settings\Screens\Abstract_Screen;
use BigCommerce\Settings\Screens\Settings_Screen;
use BigCommerce\Settings\Sections\Api_Credentials;

class Connection_Status {
	const STATUS_CACHE     = 'bigcommerce_connection_status';
	const STATUS_CACHE_TTL = 60;

	/**
	 * @var CatalogApi
	 */
	private $client;

	private $configuration_status = false;

	/**
	 * Connection_Status constructor.
	 *
	 * @param CatalogApi $client
	 * @param bool       $configuration_status Whether API configuration settings are fully in place
	 */
	public function __construct( CatalogApi $client, $configuration_status ) {
		$this->client               = $client;
		$this->configuration_status = $configuration_status;
	}

	public function register_field() {

		add_settings_field(
			'bigcommerce_connection_status',
			esc_html( __( 'Connection Status', 'bigcommerce' ) ),
			[ $this, 'render_status', ],
			Settings_Screen::NAME,
			Api_Credentials::NAME
		);
	}

	/**
	 * @action bigcommerce/settings/render/credentials
	 */
	public function render_status() {
		echo $this->get_api_status();
	}

	/**
	 * @return string
	 */
	private function get_api_status() {
		if ( ! $this->configuration_status >= Settings::STATUS_API_CONNECTED ) {
			return $this->connection_failed_message( false, null );
		}
		$status = get_transient( self::STATUS_CACHE );
		if ( ! empty( $status ) ) {
			return $status;
		}
		try {
			// throws an exception on any non-2xx response
			$response = $this->client->catalogSummaryGet();
			$status   = $this->connection_success_message();
		} catch ( ConfigurationRequiredException $e ) {
			$status = $this->connection_failed_message( false, $e );
		} catch ( ApiException $e ) {
			$status = $this->connection_failed_message( true, $e );
		}

		set_transient( self::STATUS_CACHE, $status, self::STATUS_CACHE_TTL );

		return $status;
	}

	/**
	 * Display a notice if all required credentials are not set.
	 *
	 * @param Abstract_Screen $target_screen    Settings screen the link will point to
	 * @param string[]        $excluded_screens Settings screen IDs that should not show the notice
	 *
	 * @return void
	 */
	public function credentials_required_notice( Abstract_Screen $target_screen, $excluded_screens = [] ) {
		if ( $this->configuration_status >= Settings::STATUS_CHANNEL_CONNECTED ) {
			return;
		}
		if ( function_exists( 'get_current_screen' ) ) {
			$screen = get_current_screen();
			if ( $screen && in_array( $screen->id, $excluded_screens ) ) {
				return;
			}
		}
		$message = __( 'Please connect to your BigCommerce account to start selling products on your WordPress site.', 'bigcommerce' );
		$message .= sprintf( ' <a href="%s">%s</a>', $target_screen->get_url(), __( 'Get Started →', 'bigcommerce' ) );
		printf( '<div class="notice notice-info"><p>%s</p></div>', $message );
	}

	/**
	 * @return string
	 */
	private function connection_success_message() {
		return '<p class="bigcommerce-connection-status__message bigcommerce-connection-status__message-success">' . __( 'Connected', 'bigcommerce' ) . '</p>';
	}

	/**
	 * @param string $message
	 *
	 * @return string
	 */
	private function connection_failed_message( $show_notice = false, \Exception $e = null ) {
		$status = '<p class="bigcommerce-connection-status__message bigcommerce-connection-status__message-failed">' . __( 'Not Connected', 'bigcommerce' ) . '</p>';
		$notice = '';
		if ( $show_notice ) {
			$system_status_link   = sprintf( '<a href="%s" target="_blank">%s</a>', esc_url( 'http://status.bigcommerce.com/' ), __( 'System Status', 'bigcommerce' ) );
			$contact_support_link = sprintf( '<a href="%s" target="_blank">%s</a>', esc_url( 'https://support.bigcommerce.com/contact' ), __( 'Contact Support', 'bigcommerce' ) );
			$notice               = sprintf(
				__( '<p>There was an error connecting to the BigCommerce API ( %s | %s )</p>', 'bigcommerce' ),
				$system_status_link,
				$contact_support_link
			);
			if ( WP_DEBUG && $e ) {
				$debug = $e->getMessage();
				if ( ! empty( $debug ) ) {
					$notice .= sprintf( '<p><code>%s</code></p>', esc_html( $debug ) );
				}
			}
			$notice = sprintf( '<div class="notice notice-error bigcommerce-notice">%s</div>', $notice );
		}

		return $status . $notice;
	}

	/**
	 * @return void
	 * @action load-options.php
	 */
	public function flush_status_cache() {
		delete_transient( self::STATUS_CACHE );
	}
}