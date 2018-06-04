<?php


namespace BigCommerce\Rewrites;


class Flusher {
	/**
	 * @return void
	 */
	public function schedule_flush() {
		update_option( 'bigcommerce_flushed_rewrites', 0 );
	}

	/**
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