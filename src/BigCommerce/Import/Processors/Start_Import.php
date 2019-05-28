<?php


namespace BigCommerce\Import\Processors;


use BigCommerce\Import\Runner\Status;
use BigCommerce\Logging\Error_Log;

class Start_Import implements Import_Processor {
	public function run() {
		do_action( 'bigcommerce/log', Error_Log::INFO, __( 'Starting import', 'bigcommerce' ), [] );

		// remove if left over from a previous failed import
		delete_option( Listing_Fetcher::PRODUCT_LISTING_MAP );
		delete_option( Product_Data_Fetcher::FILTERED_LISTING_MAP );

		$status = new Status();
		$status->set_status( Status::STARTED );
	}
}