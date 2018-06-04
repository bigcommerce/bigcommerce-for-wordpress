<?php


namespace BigCommerce\Forms;


class Success_Handler {
	const PARAM = 'bc-message';

	/**
	 * @param string $message  The success message to display to the user after redirect
	 * @param string $redirect URL to redirect to. Leave empty to reload the current URL
	 *
	 * @return void
	 * @action bigcommerce/form/success
	 */
	public function form_success( $message = '', $redirect = '' ) {

		$key = uniqid( 'bc', true );
		set_transient( $key,
			[
				'message' => $message,
				'user_id' => get_current_user_id(),
			],
			MINUTE_IN_SECONDS
		);

		$url = remove_query_arg( [ self::PARAM, Error_Handler::PARAM ], $redirect ?: false );
		$url = add_query_arg( [
			self::PARAM => $key,
		], $url );

		do_action( 'bigcommerce/form/redirect', $url );
	}
}