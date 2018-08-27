<?php

/**
 * Shim for functions added in WordPress 4.9
 */

if ( ! function_exists( 'wp_unschedule_hook' ) ) {

	/**
	 * Unschedules all events attached to the hook.
	 *
	 * Can be useful for plugins when deactivating to clean up the cron queue.
	 *
	 * @since 4.9.0
	 *
	 * @param string $hook Action hook, the execution of which will be unscheduled.
	 */
	function wp_unschedule_hook( $hook ) {
		$crons = _get_cron_array();

		foreach( $crons as $timestamp => $args ) {
			unset( $crons[ $timestamp ][ $hook ] );

			if ( empty( $crons[ $timestamp ] ) ) {
				unset( $crons[ $timestamp ] );
			}
		}

		_set_cron_array( $crons );
	}
}