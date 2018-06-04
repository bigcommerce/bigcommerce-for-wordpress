<?php


namespace BigCommerce\Import\Runner;


class Lock {
	const OPTION = 'bigcommerce_import.lock';

	public function get_lock() {
		return get_option( self::OPTION, 0 );
	}

	public function release_lock() {
		update_option( self::OPTION, 0, false );
	}

	public function set_lock() {
		update_option( self::OPTION, microtime( true ), false );
	}
}