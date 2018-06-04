<?php


namespace BigCommerce\Import\Processors;


use BigCommerce\Import\Runner\Status;

class Start_Import implements Import_Processor {
	public function run() {
		$status = new Status();
		$status->set_status( Status::STARTED );
	}
}