<?php


namespace BigCommerce\Rewrites;

/**
 * Handles flushing and scheduling of WordPress rewrite rules for the BigCommerce plugin.
 */
class Flusher {
    /**
     * Schedules a rewrite flush by setting an option to mark rewrites as pending.
     *
     * This method updates the `bigcommerce_flushed_rewrites` option to `0`,
     * signaling that a rewrite flush is required.
     *
     * @return void
     */
    public function schedule_flush() {
        update_option( 'bigcommerce_flushed_rewrites', 0 );
    }

    /**
     * Executes a rewrite flush if it hasn't been performed yet.
     *
     * This method checks the `bigcommerce_flushed_rewrites` option. If it's not set to `1`,
     * it flushes the rewrite rules and updates the option to `1`, preventing unnecessary future flushes.
     *
     * Hook: Triggered on the `wp_loaded` action.
     *
     * @return void
     * @action wp_loaded
     */
    public function do_flush() {
        if ( ( (int) get_option( 'bigcommerce_flushed_rewrites', 0 ) ) !== 1 ) {
            flush_rewrite_rules();
            update_option( 'bigcommerce_flushed_rewrites', 1 );
        }
    }
}