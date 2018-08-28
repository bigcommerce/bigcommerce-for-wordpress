<?php


namespace BigCommerce\Settings;


use BigCommerce\Api\ConfigurationRequiredException;
use BigCommerce\Api\v3\ApiException;
use BigCommerce\Api\v3\Api\CatalogApi;

class Connection_Status {
	const STATUS_CACHE     = 'bigcommerce_connection_status';
	const STATUS_CACHE_TTL = 60;

	/**
	 * @var CatalogApi
	 */
	private $client;

	private $configured = false;

	/**
	 * Connection_Status constructor.
	 *
	 * @param CatalogApi $client
	 * @param bool       $configured Whether API configuration settings are fully in place
	 */
	public function __construct( CatalogApi $client, $configured ) {
		$this->client     = $client;
		$this->configured = $configured;
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
		if ( ! $this->configured ) {
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
	 * @param Settings_Screen $settings_page
	 *
	 * @return void
	 */
	public function credentials_required_notice( Settings_Screen $settings_page ) {
		if ( $this->configured ) {
			return;
		}
		$message = __( 'Please connect to your BigCommerce account to start using products.', 'bigcommerce' );
		if ( function_exists( 'get_current_screen' ) ) {
			$screen = get_current_screen();
			if ( $screen && $screen->id !== $settings_page->get_hook_suffix() ) {
				$message .= sprintf( ' <a href="%s">%s</a>', $settings_page->get_url(), __( 'Get Started →', 'bigcommerce' ) );
			}
		}
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
			$notice = sprintf( '<div class="notice notice-error">%s</div>', $notice );
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