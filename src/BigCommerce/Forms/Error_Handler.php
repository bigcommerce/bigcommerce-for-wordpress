<?php


namespace BigCommerce\Forms;

/**
 * Handles form error management, including storing and retrieving errors for redirection.
 */
class Error_Handler {
	/**
	 * The query parameter used to pass error information.
	 *
	 * @var string
	 */
	const PARAM = 'bc-error';

	/**
	 * Handle form errors by storing them in a transient and redirecting to the error page.
	 *
	 * @param \WP_Error $errors The WP_Error object containing the error details.
	 * @param array     $submission The form submission data.
	 * @param string    $redirect The URL to redirect to after processing the error.
	 *
	 * @return void
	 * @action bigcommerce/form/error
	 */
	public function form_error( \WP_Error $errors, $submission, $redirect = '' ) {
		$key = uniqid( 'bc', true );
		set_transient( $key,
			[
				'error'      => $errors,
				'submission' => $submission,
				'user_id'    => get_current_user_id(),
			],
			MINUTE_IN_SECONDS
		);

		$url = remove_query_arg( [ self::PARAM, Success_Handler::PARAM ], $redirect ?: false );
		$url = add_query_arg( [
			self::PARAM => $key,
		], $url );

		/**
		 * Fires when a form is about to redirect due to an error.
		 *
		 * @param string $url The URL to redirect to, with error parameters added.
		 */
		do_action( 'bigcommerce/form/redirect', $url );
	}

	/**
	 * Retrieve any errors stored in the transient for the current user.
	 *
	 * @param \WP_Error|null $data Existing error data to potentially override.
	 *
	 * @return \WP_Error|null The error data, or null if no errors exist.
	 * @filter bigcommerce/form/messages/error
	 */
	public function get_errors( $data ) {
		if ( $data ) {
			return $data; // don't override if already set
		}

		if ( empty( $_REQUEST[ 'bc-error' ] ) ) {
			return $data;
		}

		$bc_error    = filter_var_array( $_REQUEST, [ 'bc-error' => FILTER_SANITIZE_STRING ] );
		$stored_data = get_transient( $bc_error[ 'bc-error' ] );
		if ( empty( $stored_data[ 'error' ] ) || ! array_key_exists( 'user_id', $stored_data ) ) {
			return $data;
		}
		if ( $stored_data[ 'user_id' ] != get_current_user_id() ) {
			return $data;
		}
		if ( ! is_wp_error( $stored_data[ 'error' ] ) || count( $stored_data[ 'error' ]->get_error_codes() ) < 1 ) {
			return $data;
		}

		return $stored_data;
	}
}