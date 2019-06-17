<?php

/**
 * Shim for functions added in WordPress 5.1
 */

if ( ! function_exists( 'wp_remove_targeted_link_rel_filters' ) ) {

	/**
	 * Removes the filters added by wp_init_targeted_link_rel_filters()
	 */
	function wp_remove_targeted_link_rel_filters() {
		// do nothing, since the companion function doesn't exist either
	}
}