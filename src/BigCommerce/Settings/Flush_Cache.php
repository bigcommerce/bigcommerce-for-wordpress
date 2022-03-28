<?php declare(strict_types=1);

namespace BigCommerce\Settings;

use BigCommerce\Import\No_Cache_Options;
use BigCommerce\Import\Processors\Cleanup;
use BigCommerce\Import\Runner\Status;
use BigCommerce\Logging\Error_Log;
use BigCommerce\Post_Types\Product\Product;
use BigCommerce\Settings\Sections\Troubleshooting_Diagnostics;

class Flush_Cache {

	const NAME = 'bigcommerce_flush_cache_scheduler';

	protected $screen_settings;

	public function __construct( $screen_settings ) {
		$this->screen_settings = $screen_settings;
	}

	/**
	 * Handle cache flush request
	 */
	public function handle_request() {
		$submission = filter_var_array( $_GET, [
			'_wpnonce' => FILTER_SANITIZE_STRING,
			'action'   => FILTER_SANITIZE_STRING,
		] );

		if ( empty( $submission['_wpnonce'] ) || ! wp_verify_nonce( $submission['_wpnonce'], $submission['action'] ) ) {
			throw new \InvalidArgumentException( __( 'Invalid request. Please try again.', 'bigcommerce' ), 403 );
		}

		if ( $submission['action'] === Troubleshooting_Diagnostics::FLUSH_USER ) {
			wp_schedule_single_event( time(), Cleanup::CLEAN_USERS_TRANSIENT );
		} elseif ( $submission['action'] === Troubleshooting_Diagnostics::FLUSH_PRODUCTS ) {
			wp_schedule_single_event( time(), Cleanup::CLEAN_PRODUCTS_TRANSIENT, [
				'offset' => 0,
			] );
		}

		$redirect = esc_url_raw( $this->screen_settings->get_url() );
		$redirect = add_query_arg( [ 'settings-updated' => 1 ], $redirect );
		add_settings_error( self::NAME, 200, __( 'Cache flush is scheduled', 'bigcommerce' ), 'updated' );
		set_transient( 'settings_errors', get_settings_errors(), 30 );
		wp_safe_redirect( $redirect, 303 );

		exit;
	}

}
