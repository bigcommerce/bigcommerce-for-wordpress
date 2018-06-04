<?php


namespace BigCommerce\Import\Processors;


use BigCommerce\Import\Runner\Status;

class Error_Handler implements Import_Processor {
	public function run() {

		/** @var \wpdb $wpdb */
		global $wpdb;
		$wpdb->query( "TRUNCATE TABLE {$wpdb->bc_import_queue}" ); // make sure it's good and clean
		delete_option( Product_ID_Fetcher::STATE_OPTION );

		$status = new Status();
		$status->set_status( Status::FAILED );
		$status->rotate_logs();
	}
}