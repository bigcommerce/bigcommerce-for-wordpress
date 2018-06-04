<?php


namespace BigCommerce\Import\Runner;


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
		do_action( 'bigcommerce/import/before', $current[ 'status' ] );
		do_action( 'bigcommerce/import/run/status=' . $current[ 'status' ] );
		do_action( 'bigcommerce/import/after', $current[ 'status' ] );
		$this->schedule_next();
		$lock->release_lock();
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