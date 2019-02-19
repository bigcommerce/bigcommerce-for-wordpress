<?php

namespace BigCommerce\Import;

/**
 * Trait No_Cache_Options
 *
 * Wrappers around the WordPress options API that bypasses
 * caching, ensuring that data is always read from and
 * written to the database.
 *
 * This solves the issue of long running processes sometimes
 * developing stale caches in memory, then failing to
 * update the database when values change.
 */
trait No_Cache_Options {

	/**
	 * @param string $option
	 * @param bool   $default
	 *
	 * @return mixed
	 */
	protected function get_option( $option, $default = false ) {
		$this->clear_cache( $option );

		return get_option( $option, $default );
	}

	/**
	 * @param string $option
	 * @param mixed  $value
	 * @param bool   $autoload
	 *
	 * @return bool
	 */
	protected function update_option( $option, $value, $autoload = false ) {
		$this->clear_cache( $option );

		return update_option( $option, $value, $autoload );
	}

	/**
	 * @param string $option
	 * @param mixed  $value
	 * @param bool   $autoload
	 *
	 * @return bool
	 */
	protected function add_option( $option, $value, $autoload = false ) {
		$this->clear_cache( $option );

		return add_option( $option, $value, $autoload );
	}

	/**
	 * @param string $option
	 *
	 * @return bool
	 */
	protected function delete_option( $option ) {
		// WP already goes directly to DB when deleting an option
		return delete_option( $option );
	}

	/**
	 * Clear caches that may be storing the option
	 *
	 * @param string $option
	 *
	 * @return void
	 */
	private function clear_cache( $option ) {
		$alloptions = wp_load_alloptions();
		if ( is_array( $alloptions ) && isset( $alloptions[ $option ] ) ) {
			unset( $alloptions[ $option ] );
			wp_cache_set( 'alloptions', $alloptions, 'options' );
		}
		wp_cache_delete( $option, 'options' );
	}
}