<?php


namespace BigCommerce\Forms;


class Success_Handler {
	const PARAM = 'bc-message';

	/**
	 * @param string $message  The success message to display to the user after redirect
	 * @param array  $submission The data submitted with the form
	 * @param string $redirect URL to redirect to. Leave empty to reload the current URL
	 * @param array  $data     Optional data to store with the message
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

		do_action( 'bigcommerce/form/redirect', $url );
	}
}