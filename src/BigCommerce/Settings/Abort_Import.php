<?php declare(strict_types=1);

namespace BigCommerce\Settings;

use BigCommerce\Import\No_Cache_Options;
use BigCommerce\Import\Processors\Cleanup;
use BigCommerce\Import\Runner\Status;
use BigCommerce\Logging\Error_Log;

class Abort_Import {

	use No_Cache_Options;

	const ABORT_IMPORT_OPTION = 'bigcommerce_abort_import_option';

	protected $screen_settings;

	public function __construct( $screen_settings ) {
		$this->screen_settings = $screen_settings;
	}

	/**
	 * Abort import process, make cleanup and set self::ABORT_IMPORT_OPTION option in order
	 * to detect on next stages that import was aborted
	 *
	 * @param \BigCommerce\Import\Processors\Cleanup $cleanup
	 */
	public function abort( Cleanup $cleanup ) {
		$submission = filter_var_array( $_GET, [
			'_wpnonce' => FILTER_SANITIZE_STRING,
		] );

		if ( empty( $submission['_wpnonce'] ) || ! wp_verify_nonce( $submission['_wpnonce'], Sections\Troubleshooting_Diagnostics::ABORT_NAME ) ) {
			throw new \InvalidArgumentException( __( 'Invalid request. Please try again.', 'bigcommerce' ), 403 );
		}

		$status            = new Status();
		$current           = $status->current_status();
		$rejected_statuses = [ Status::NOT_STARTED, Status::FAILED, Status::COMPLETED ];

		/**
		 * We won't run the cleanup if import is not performed, failed or completed
		 */
		if ( ! in_array( $current['status'], $rejected_statuses ) ) {
			do_action( 'bigcommerce/log', Error_Log::NOTICE, __( 'Import process has been aborted. Run import cleanup', 'bigcommerce' ), [] );
			$cleanup->run( true );
			$this->update_option( self::ABORT_IMPORT_OPTION, true );
		}

		wp_safe_redirect( esc_url_raw( $this->screen_settings->get_url() ), 303 );
		exit();
	}

}
