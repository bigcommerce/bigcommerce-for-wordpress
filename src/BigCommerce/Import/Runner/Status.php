<?php


namespace BigCommerce\Import\Runner;


use BigCommerce\Import\No_Cache_Options;

class Status {
	use No_Cache_Options;

	const NOT_STARTED              = 'not_started';
	const STARTED                  = 'started';
	const FETCHING_LISTINGS        = 'fetching_listings';
	const FETCHED_LISTINGS         = 'fetched_listings';
	const INITIALIZING_CHANNEL     = 'initializing_channel';
	const INITIALIZED_CHANNEL      = 'initialized_channel';
	const FETCHING_PRODUCTS        = 'fetching_products';
	const FETCHED_PRODUCTS         = 'fetched_products';
	const MARKING_DELETED_PRODUCTS = 'marking_deleted_products';
	const MARKED_DELETED_PRODUCTS  = 'marked_deleted_products';
	const PROCESSING_QUEUE         = 'processing_queue';
	const PROCESSED_QUEUE          = 'processed_queue';
	const FETCHING_STORE           = 'fetching_store';
	const FETCHED_STORE            = 'fetched_store';
	const FETCHING_CURRENCIES      = 'fetching_currencies';
	const FETCHED_CURRENCIES       = 'fetched_currencies';
	const CLEANING                 = 'cleaning';
	const COMPLETED                = 'completed';
	const FAILED                   = 'failed';
	const UPDATING_CATEGORIES      = 'updating_categories';
	const UPDATED_CATEGORIES       = 'updated_categories';
	const UPDATING_BRANDS          = 'updating_brands';
	const UPDATED_BRANDS           = 'updated_brands';
	const PURGING_CATEGORIES       = 'purging_categories';
	const PURGED_CATEGORIES        = 'purged_categories';
	const PURGING_BRANDS           = 'purging_brands';
	const PURGED_BRANDS            = 'purged_brands';
	const RESIZING_IMAGES          = 'resizing_images';
	const RESIZED_IMAGES           = 'resized_images';

	const CURRENT_LOG  = 'bigcommerce_current_import_status_log';
	const PREVIOUS_LOG = 'bigcommerce_previous_import_status_log';

	/**
	 * @return array The `timestamp` and `status` of the last update to the current import
	 */
	public function current_status() {
		return $this->get_status( self::CURRENT_LOG );
	}

	/**
	 * @return array The `timestamp` and `status` of the last update to the previous import
	 */
	public function previous_status() {
		return $this->get_status( self::PREVIOUS_LOG );
	}

	/**
	 * Add a status to the log for the current import. The status will be
	 * appended to the log, even if it is the same as the current status.
	 *
	 * @param string $status
	 *
	 * @return void
	 */
	public function set_status( $status ) {
		$log = $this->current_log();

		// cast timestamp to string to preserve microtime
		$log[ (string) microtime( true ) ] = $status;
		$this->update_option( self::CURRENT_LOG, $log, false );
		do_action( 'bigcommerce/import/set_status', $status );
	}

	/**
	 * Overwrite the previous log with the current log and empty the current log
	 *
	 * @return void
	 */
	public function rotate_logs() {
		$log = $this->current_log();
		/**
		 * Rotate out the current log into the previous log slot
		 *
		 * @param array $log The current log
		 */
		do_action( 'bigcommerce/import/logs/rotate', $log );
		$this->update_option( self::PREVIOUS_LOG, $log, false );
		$this->update_option( self::CURRENT_LOG, [], false );
		/**
		 * Logs have been rotated
		 *
		 * @param array $log The previous log
		 */
		do_action( 'bigcommerce/import/logs/rotated', $log );
	}

	/**
	 * @return array
	 */
	private function current_log() {
		return $this->get_log( self::CURRENT_LOG );
	}

	/**
	 * @return array
	 */
	private function previous_log() {
		return $this->get_log( self::PREVIOUS_LOG );
	}


	/**
	 * @param string $which Log to get the status from
	 *
	 * @return array The `timestamp` and `status` of the last update
	 *               to the indicated log
	 */
	private function get_status( $which ) {
		$log = $this->get_log( $which );
		if ( empty( $log ) ) {
			return [
				'timestamp' => 0,
				'status'    => self::NOT_STARTED,
			];
		}
		$status    = end( $log );
		$timestamp = key( $log );

		return [
			'timestamp' => floatval( $timestamp ),
			'status'    => $status,
		];
	}

	/**
	 * @param string $which The name of the option storing the log.
	 *
	 * @return array
	 */
	private function get_log( $which ) {
		$log = $this->get_option( $which, [] );
		if ( ! is_array( $log ) ) {
			$log = [];
		}
		ksort( $log );

		return $log;
	}
}
