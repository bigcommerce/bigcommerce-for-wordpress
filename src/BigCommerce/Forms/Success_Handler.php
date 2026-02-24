<?php 


namespace BigCommerce\Forms;

/**
 * Handles the success of form submissions by storing a success message, form submission data,
 * and optionally redirecting the user to a specified URL or reloading the current page.
 *
 * @package BigCommerce\Forms
 */
class Success_Handler {

	/**
	 * The query parameter used for storing the transient key in the URL.
	 *
	 * @var string
	 */
	const PARAM = 'bc-message';

	/**
	 * Handles the success of a form submission.
	 *
	 * This method stores the success message, form submission data, and optional extra data as a transient,
	 * and redirects the user to the provided URL (or reloads the current page if no URL is specified).
	 * The transient stores the data temporarily and can be used to display a success message after the redirect.
	 *
	 * @param string $message  The success message to display to the user after redirect.
	 * @param array  $submission The data submitted with the form.
	 * @param string $redirect The URL to redirect to. Leave empty to reload the current URL.
	 * @param array  $data     Optional data to store with the message (e.g., additional context or metadata).
	 *
	 * @return void
	 * @action bigcommerce/form/success
	 */
	public function form_success( $message = '', $submission = [], $redirect = '', $data = [] ) {

		$transient_key = uniqid( 'bc', true );
		set_transient( $transient_key,
			[
				'message' => $message,
				'submission' => $submission,
				'data'    => $data,
				'user_id' => get_current_user_id(),
			],
			MINUTE_IN_SECONDS
		);

		$url = remove_query_arg( [ self::PARAM, Error_Handler::PARAM ], $redirect ?: false );
		$url = add_query_arg( [
			self::PARAM => $transient_key,
		], $url );

		/**
		 * Triggered when a form is successfully processed.
		 *
		 * @param string $url The URL to redirect the user to
		 */
		do_action( 'bigcommerce/form/redirect', $url );
	}
}