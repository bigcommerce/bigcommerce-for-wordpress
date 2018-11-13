<?php


namespace BigCommerce\Forms;


use BigCommerce\Accounts\Customer;

class Delete_Address_Handler implements Form_Handler {
	const ACTION = 'delete-address';

	public function handle_request( $submission ) {
		if ( ! $this->should_handle_request( $submission ) ) {
			return;
		}

		$errors = $this->validate_submission( $submission );

		if ( count( $errors->get_error_codes() ) > 0 ) {
			do_action( 'bigcommerce/form/error', $errors, $submission );

			return;
		}

		$customer   = new Customer( get_current_user_id() );
		$address_id = (int) $submission[ 'address-id' ];
		$customer->delete_address( $address_id );


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

		$errors = apply_filters( 'bigcommerce/form/delete_address/errors', $errors, $submission );

		return $errors;
	}
}