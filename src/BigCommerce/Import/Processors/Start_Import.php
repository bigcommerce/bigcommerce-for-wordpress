<?php


namespace BigCommerce\Import\Processors;


use BigCommerce\Import\Runner\Status;
use BigCommerce\Logging\Error_Log;

class Start_Import implements Import_Processor {
	public function run() {
		do_action( 'bigcommerce/log', Error_Log::INFO, __( 'Starting import', 'bigcommerce' ), [] );
		$status = new Status();
		$status->set_status( Status::STARTED );
	}
}