<?php


namespace BigCommerce\Import\Processors;


use BigCommerce\Import\Runner\Status;
use BigCommerce\Post_Types\Queue_Task\Queue_Task;

class Error_Handler implements Import_Processor {
	public function run() {

		/** @var \wpdb $wpdb */
		global $wpdb;

		$wpdb->update(
			$wpdb->posts,
			[ 'post_status' => 'trash', ],
			[ 'post_type' => Queue_Task::NAME, ],
			[ '%s' ],
			[ '%s' ]
		);

		delete_option( Listing_Fetcher::PRODUCT_LISTING_MAP );
		delete_option( Product_Data_Fetcher::FILTERED_LISTING_MAP );

		$status = new Status();
		$status->set_status( Status::FAILED );
		$status->rotate_logs();
	}
}