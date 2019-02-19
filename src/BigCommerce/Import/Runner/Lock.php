<?php


namespace BigCommerce\Import\Runner;


use BigCommerce\Import\No_Cache_Options;

class Lock {
	use No_Cache_Options;

	const OPTION = 'bigcommerce_import.lock';

	public function get_lock() {
		return (float) $this->get_option( self::OPTION, 0 );
	}

	public function release_lock() {
		$this->update_option( self::OPTION, 0, false );
	}

	public function set_lock() {
		$this->update_option( self::OPTION, microtime( true ), false );
	}
}