<?php

namespace BigCommerce\Import\Runner;

use BigCommerce\Settings\Import;

/**
 * Class Cron_Monitor
 *
 * Makes sure that the import tasks are schedule appropriately
 */
class Cron_Monitor {
	/**
	 * @var int Number of seconds to wait before an import is considered
	 *          to have timed out.
	 */
	private $timeout;

	public function __construct( $timeout ) {
		$this->timeout = $timeout;
	}

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
		if ( $current[ 'status' ] == Status::NOT_STARTED ) {
			$scheduler->schedule_next_import();
			$lock->release_lock();

			return;
		}

		if ( ! $lock->get_lock() || $current[ 'timestamp' ] < time() - $this->timeout ) {
			$scheduler->schedule_next_batch();
			$lock->release_lock();

			return;
		}

		// there must be an import running right now
	}
}