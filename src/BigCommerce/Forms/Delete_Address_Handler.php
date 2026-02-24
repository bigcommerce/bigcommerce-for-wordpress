<?php


namespace BigCommerce\Forms;


use BigCommerce\Accounts\Customer;

/**
 * Handles the deletion of a customer's address from their account.
 */
class Delete_Address_Handler implements Form_Handler {
	/** 
	 * The action identifier for deleting an address.
	 *
	 * @var string
	 */
	const ACTION = 'delete-address';

	/**
	 * Handle the address deletion request.
	 *
	 * @param array $submission The form submission data.
	 */
	public function handle_request( $submission ) {
		if ( ! $this->should_handle_request( $submission ) ) {
			return;
		}

		$errors = $this->validate_submission( $submission );

		if ( count( $errors->get_error_codes() ) > 0 ) {
			/**
			 * Triggered when an error occurs during a form submission. Allows developers to handle or log form submission errors.
			 *
			 * @param \WP_Error $error      The error object representing the validation or processing error. Contains error codes and messages.
			 * @param array     $submission The sanitized form submission data (usually $_POST).
			 * @param string    $redirect   The URL to redirect the user after processing the error. Defaults to the home URL.
			 */
			do_action( 'bigcommerce/form/error', $errors, $submission );

			return;
		}

		$customer   = new Customer( get_current_user_id() );
		$address_id = (int) $submission[ 'address-id' ];
		$customer->delete_address( $address_id );

		/**
		 * Handles form success and processes related actions.
		 *
		 * @param string $message The success message.
		 * @param array $submission The form submission data.
		 * @param string|null $url Optional redirect URL.
		 * @param array $data Additional data associated with the submission.
		 *
		 * @return void
		 */
		do_action( 'bigcommerce/form/success', __( 'Address deleted.', 'bigcommerce' ), $submission, null, [ 'key' => 'address_deleted' ] );
	}

	private function should_handle_request( $submission ) {
		if ( ! is_user_logged_in() ) {
			return false;
		}
		if ( empty( $submission[ 'bc-action' ] ) || $submission[ 'bc-action' ] !== self::ACTION ) {
			return false;
		}
		if ( empty( $submission[ '_wpnonce' ] ) || empty( $submission[ 'address-id' ] ) ) {
			return false;
		}

		return true;
	}

	private function validate_submission( $submission ) {
		$errors = new \WP_Error();

		if ( ! wp_verify_nonce( $submission[ '_wpnonce' ], self::ACTION . $submission[ 'address-id' ] ) ) {
			$errors->add( 'invalid_nonce', __( 'There was an error validating your request. Please try again.', 'bigcommerce' ) );
		}

		/**
		 * Filters delete address form errors.
		 *
		 * @param \WP_Error $errors     WP error.
		 * @param array     $submission Submitted data.
		 */
		$errors = apply_filters( 'bigcommerce/form/delete_address/errors', $errors, $submission );

		return $errors;
	}
}
