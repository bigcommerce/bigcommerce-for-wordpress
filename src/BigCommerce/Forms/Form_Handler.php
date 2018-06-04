<?php

namespace BigCommerce\Forms;

interface Form_Handler {
	/**
	 * Handle a submission for the form
	 *
	 * @param array $submission
	 *
	 * @return void
	 */
	public function handle_request( $submission );
}
