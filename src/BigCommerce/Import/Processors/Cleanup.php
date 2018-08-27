<?php


namespace BigCommerce\Import\Processors;


use BigCommerce\Import\Runner\Cron_Runner;
use BigCommerce\Import\Runner\Status;

class Cleanup implements Import_Processor {
	public function run() {
		$status = new Status();
		$status->set_status( Status::CLEANING );

		/** @var \wpdb $wpdb */
		global $wpdb;
		$wpdb->query( "TRUNCATE TABLE {$wpdb->bc_import_queue}" ); // make sure it's good and clean
		delete_option( Product_ID_Fetcher::STATE_OPTION );

		$status->set_status( Status::COMPLETED );

		wp_unschedule_hook( Cron_Runner::START_CRON );
		wp_unschedule_hook( Cron_Runner::CONTINUE_CRON );

		$status->rotate_logs(); // must rotate _after_ status set to complete
	}
}