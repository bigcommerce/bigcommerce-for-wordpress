<?php


namespace BigCommerce\Import\Runner;


use BigCommerce\Manager\Manager;

/**
 * Handles the scheduling and execution of BigCommerce import processes via WordPress cron.
 */
class Cron_Runner {
	const START_CRON    = 'bigcommerce_start_import';
	const CONTINUE_CRON = 'bigcommerce_continue_import';

	/**
	 * @return void
	 * @action self::START_CRON
	 */
	public function start_import() {
		$lock = new Lock();
		if ( $lock->get_lock() ) {
			return; // another process has claimed it
		}

		$status  = new Status();
		$current = $status->current_status();
		if ( $current[ 'status' ] !== Status::NOT_STARTED ) {
			return; // it's already running
		}

		$lock->set_lock();
		/**
		 * This action is triggered at the start of the BigCommerce import process. It clears or truncates the log file to ensure that old logs do not interfere with the new import process.
		 */
		do_action( 'bigcommerce/import/start' );
		$this->schedule_next();
		$lock->release_lock();
	}

	/**
	 * @return void
	 * @action self::CONTINUE_CRON
	 */
	public function continue_import() {

		$status  = new Status();
		$current = $status->current_status();
		if ( $current[ 'status' ] === Status::NOT_STARTED ) {
			return; // nothing to continue
		}

		$lock = new Lock();
		if ( $lock->get_lock() ) {
			return; // another process has claimed it
		}
		$lock->set_lock();
		/**
		 * Hook before the import starts.
		 *
		 * @param string $status Current status of the import.
		 */	
		do_action( 'bigcommerce/import/before', $current[ 'status' ] );
		/**
		 * Hook to run the import process.
		 *
		 * @param string $status Current status of the import.
		 */
		do_action( 'bigcommerce/import/run', $current[ 'status' ] );
		/**
		 * Hook after the import finishes.
		 *
		 * @param string $status Current status of the import.
		 */
		do_action( 'bigcommerce/import/after', $current[ 'status' ] );
		$this->schedule_next();
		$lock->release_lock();
	}

	/**
	 * When an ajax request to get the current import status comes in,
	 * run the next step in the process by triggering the scheduled
	 * cron job.
	 *
	 * Runs at priority 5, before the ajax response handler
	 *
	 * @return void
	 * @action Import_Status::AJAX_ACTION_IMPORT_STATUS 5
	 */
	public function ajax_continue_import() {
		try {
			// in case there's already an event scheduled, remove it
			wp_unschedule_hook( self::CONTINUE_CRON );
			wp_unschedule_hook( Manager::CRON_PROCESSOR );
			// Then fire the action that the cron would have fired on the schedule.
			do_action( self::CONTINUE_CRON );

			// This will have the side effect of scheduling the next run, so the
			// cron can continue to run if the user leaves the page and the Ajax
			// requests stop coming.
		} catch ( \Exception $e ) {
			$message = __( 'The server sent an unexpected response. We’ll keep trying, so dont’t worry. If the problem persists, try turning on error logging in the Diagnostics settings panel.', 'bigcommerce' );
			if ( WP_DEBUG && $e->getMessage() ) {
				$message .= ' ' . sprintf( __( 'Error message: %s', 'bigcommerce' ), $e->getMessage() );
			}
			wp_send_json_error( [
				'code'    => 'internal_server_error',
				'message' => $message,
			], 500 );
		}
	}

	private function schedule_next() {
		$status  = new Status();
		$current = $status->current_status();

		$scheduler = new Cron_Scheduler();
		if ( $current[ 'status' ] === Status::NOT_STARTED ) {
			$scheduler->schedule_next_import();
		} else {
			$scheduler->schedule_next_batch();
		}
	}
}
