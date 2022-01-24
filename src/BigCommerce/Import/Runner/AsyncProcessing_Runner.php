<?php

namespace BigCommerce\Import\Runner;


use BigCommerce\Logging\Error_Log;

class AsyncProcessing_Runner {

	const CONTINUE_IMPORT = 'bigcommerce_async_import_continue';

	/**
	 * Perform additional 'bigcommerce/import/run' action in order to speed up import process
	 */
	public function run() {
		$lock     = new Lock();
		$status   = new Status();
		$current  = $status->current_status();
		$progress = $current['status'];

		/**
		 * Allow run only for fetching listings, initializing channels, products fetch. Mentioned task are
		 * the most time-consuming items and can be done in parallel
		 */
		if ( ! $this->is_allowed_status( $progress ) ) {
			do_action( 'bigcommerce/log', Error_Log::INFO, __( 'Exit from 2nd thread because status is not allowed', 'bigcommerce' ), [
					'status' => $progress,
			] );

			return;
		}

		if ( $lock->get_lock() ) {
			do_action( 'bigcommerce/log', Error_Log::INFO, __( 'Thread busy with another process', 'bigcommerce' ), [
				'status' => $progress,
			] );

			return; // another process has claimed it
		}

		$lock->set_lock();

		do_action( 'bigcommerce/import/before', $progress );
		do_action( 'bigcommerce/import/run', $progress );
		do_action( 'bigcommerce/import/after', $progress );

		$lock->release_lock();
	}

	/**
	 * Is process has allowed status
	 *
	 * @param $status
	 *
	 * @return bool
	 */
	private function is_allowed_status( $status ) {
		$status_list = [
			Status::FETCHING_PRODUCTS,
			Status::INITIALIZED_CHANNEL,
			Status::PROCESSING_QUEUE,
		];

		$is_initialized_channel = strpos( $status, Status::INITIALIZING_CHANNEL ) !== false;
		$is_fetched_listings    = strpos( $status, Status::FETCHING_LISTINGS ) !== false;

		return in_array( $status, $status_list ) || $is_initialized_channel || $is_fetched_listings;
	}
}
