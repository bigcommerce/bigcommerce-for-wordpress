<?php


namespace BigCommerce\Import\Runner;


class CLI_Runner {
	const RESPONSE_SUCCESS = 0;
	const RESPONSE_LOCKED  = 1;
	const RESPONSE_ERROR   = 2;

	public function run() {
		$lock = new Lock();
		if ( $lock->get_lock() ) {
			return self::RESPONSE_LOCKED;
		}
		$lock->set_lock();

		$status  = new Status();
		$current = $status->current_status();
		if ( $current[ 'status' ] === Status::NOT_STARTED ) {
			do_action( 'bigcommerce/import/start' );
		}

		$current = $status->current_status();
		while ( $current[ 'status' ] !== Status::NOT_STARTED ) {
			$lock->set_lock();

			do_action( 'bigcommerce/import/before', $current[ 'status' ] );
			do_action( 'bigcommerce/import/run', $current[ 'status' ] );
			do_action( 'bigcommerce/import/after', $current[ 'status' ] );

			$current = $status->current_status();
		};

		$lock->release_lock();

		$previous = $status->previous_status();
		if ( $previous[ 'status' ] == Status::FAILED ) {
			return self::RESPONSE_ERROR;
		}

		return self::RESPONSE_SUCCESS;
	}
}