<?php

namespace BigCommerce\Forms;

/**
 * Interface for handling form submissions.
 */
interface Form_Handler {
	/**
	 * Handle a submission for the form.
	 *
	 * This method is called to process the form submission data.
	 *
	 * @param array $submission The submitted form data.
	 *
	 * @return void
	 */
	public function handle_request( $submission );
}
