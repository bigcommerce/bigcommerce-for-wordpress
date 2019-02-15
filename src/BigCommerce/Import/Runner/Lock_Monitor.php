<?php

namespace BigCommerce\Import\Runner;

use BigCommerce\Logging\Error_Log;

/**
 * Class Lock_Monitor
 *
 * Makes sure that the import lock expires if the timeout is reached
 */
class Lock_Monitor {
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
	 * @action init 0
	 */
	public function check_for_expired_lock() {
		$lock = new Lock();
		if ( ! $lock->get_lock() ) {
			return; // no lock in place, all is well
		}

		$status  = new Status();
		$current = $status->current_status();
		if ( $current[ 'status' ] === Status::NOT_STARTED ) {
			$lock->release_lock(); // there shouldn't be a lock if no import is in progress

			return;
		}

		if ( $current[ 'timestamp' ] < time() - $this->timeout ) {
			do_action( 'bigcommerce/log', Error_Log::INFO, __( 'Releasing expired import lock', 'bigcommerce' ), [
				'status' => $current,
			] );
			$lock->release_lock(); // the lock is expired

			return;
		}

	}
}