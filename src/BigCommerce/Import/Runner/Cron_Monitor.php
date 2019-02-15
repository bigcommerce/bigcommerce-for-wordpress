<?php

namespace BigCommerce\Import\Runner;

/**
 * Class Cron_Monitor
 *
 * Makes sure that the import tasks are scheduled appropriately
 */
class Cron_Monitor {

	/**
	 * @return void
	 * @action init
	 */
	public function check_for_scheduled_crons() {
		$next_start = wp_next_scheduled( Cron_Runner::START_CRON );
		if ( ! empty( $next_start ) ) {
			return; // we have a run scheduled, so all is well
		}
		$next_continue = wp_next_scheduled( Cron_Runner::CONTINUE_CRON );
		if ( ! empty( $next_continue ) ) {
			return; // we have a run scheduled, so all is well
		}

		$lock      = new Lock();
		$scheduler = new Cron_Scheduler();

		$status  = new Status();
		$current = $status->current_status();
		if ( $current[ 'status' ] === Status::NOT_STARTED ) {
			$scheduler->schedule_next_import();

			return;
		}

		if ( ! $lock->get_lock() ) {
			$scheduler->schedule_next_batch();

			return;
		}

		// there must be an import running right now
	}

	/**
	 * @param $old_value
	 * @param $new_value
	 *
	 * @return void
	 * @action update_option_ . Import::OPTION_FREQUENCY
	 */
	public function listen_for_changed_schedule( $old_value, $new_value ) {
		$next_continue = wp_next_scheduled( Cron_Runner::CONTINUE_CRON );
		if ( ! empty( $next_continue ) ) {
			return; // we have a run in progress, leave it alone
		}

		wp_unschedule_hook( Cron_Runner::START_CRON );

		$scheduler = new Cron_Scheduler();
		$scheduler->schedule_next_import();
	}

	/**
	 * If an import is starting, unschedule the next scheduled start.
	 * Usually means that an import is running from the CLI.
	 *
	 * @return void
	 * @action bigcommerce/import/run
	 */
	public function listen_for_import_start( $status ) {
		if ( $status === Status::STARTED ) {
			wp_unschedule_hook( Cron_Runner::START_CRON );
		}
	}
}