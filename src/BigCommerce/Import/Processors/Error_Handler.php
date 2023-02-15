<?php


namespace BigCommerce\Import\Processors;


use BigCommerce\Import\Runner\Status;
use BigCommerce\Post_Types\Queue_Task\Queue_Task;

class Error_Handler implements Import_Processor {
	public function run() {

		/** @var \wpdb $wpdb */
		global $wpdb;

		$wpdb->query(
			$wpdb->prepare(
				"UPDATE {$wpdb->posts} SET post_status = '%s' WHERE post_type = '%s'",
				'trash',
				Queue_Task::NAME
			)
		);

		delete_option( Listing_Fetcher::PRODUCT_LISTING_MAP );
		delete_option( Term_Import::BRANDS_CHECKPOINT );
		delete_option( Product_Data_Fetcher::FILTERED_LISTING_MAP );

		$status = new Status();
		$status->set_status( Status::FAILED );
		$status->rotate_logs();
	}
}
