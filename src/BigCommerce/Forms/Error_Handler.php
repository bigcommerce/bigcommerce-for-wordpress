<?php


namespace BigCommerce\Forms;


class Error_Handler {
	const PARAM = 'bc-error';

	/**
	 * @param \WP_Error $errors
	 * @param array     $submission
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

		do_action( 'bigcommerce/form/redirect', $url );
	}

	/**
	 * @param \WP_Error|null $data
	 *
	 * @return \WP_Error|null
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