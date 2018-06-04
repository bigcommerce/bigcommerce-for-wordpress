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
}