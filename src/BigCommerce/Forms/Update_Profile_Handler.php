<?php


namespace BigCommerce\Forms;


use BigCommerce\Accounts\Customer;

/**
 * Handles the profile update form submission process.
 *
 * @package BigCommerce\Forms
 */
class Update_Profile_Handler implements Form_Handler {

    /**
     * The action identifier for the form submission.
     */
    const ACTION = 'edit-profile';

    /**
     * Handles the form submission for updating the user profile.
     *
     * @param array $submission The submitted form data.
     *
     * @return void
     */
	public function handle_request( $submission ) {
		if ( ! $this->should_handle_request( $submission ) ) {
			return;
		}

		$user   = wp_get_current_user();
		$errors = $this->validate_submission( $submission, $user );

		$password = $submission[ 'bc-profile' ][ 'new_password' ];
		// prevent logging sensitive information in plain text
		unset( $submission[ 'bc-profile' ][ 'current_password' ] );
		unset( $submission[ 'bc-profile' ][ 'new_password' ] );
		unset( $submission[ 'bc-profile' ][ 'confirm_password' ] );

		if ( count( $errors->get_error_codes() ) > 0 ) {
			do_action( 'bigcommerce/form/error', $errors, $submission );

			return;
		}

		$customer = new Customer( $user->ID );
		$profile  = $this->get_profile( $submission[ 'bc-profile' ] );

		$update_user = false;
		if ( ! empty( $profile[ 'email' ] ) && $profile[ 'email' ] != $user->user_email ) {
			$user->user_email = $profile[ 'email' ];
			$update_user      = true;
		}

		$userdata = $user->to_array();
		if ( $password ) {
			$userdata['user_pass']                      = $password;
			$update_user                                = true;
			$profile[ '_authentication' ][ 'password' ] = $password;
		}

		if ( $update_user ) {
			$updated = wp_update_user( $userdata );
			if ( is_wp_error( $updated ) ) {
				switch ( $updated->get_error_code() ) {
					case 'existing_user_email':
					case 'empty_user_login':
					case 'user_login_too_long':
					case 'existing_user_login':
					case 'invalid_username':
						$errors->add( 'email', $updated->get_error_message() );
						break;
					default:
						$errors->add( $updated->get_error_code(), $updated->get_error_message() );
						break;
				}
				do_action( 'bigcommerce/form/error', $errors, $submission );

				return;
			}
		}

		$success = $customer->update_profile( $profile );
		if ( ! $success ) {
			$errors->add( 'api_error', __( 'There was an error saving your request. Please try again.', 'bigcommerce' ) );
			do_action( 'bigcommerce/form/error', $errors, $submission );

			return;
		}

		/**
		 * Filters profile form success message.
		 *
		 * @param string $message Profile form success message.
		 */
		$message = apply_filters( 'bigcommerce/form/profile/success_message', __( 'Profile updated.', 'bigcommerce' ) );
		do_action( 'bigcommerce/form/success', $message, $submission, null, [ 'key' => 'profile_updated' ] );
	}

	private function should_handle_request( $submission ) {
		if ( ! is_user_logged_in() ) {
			return false;
		}
		if ( empty( $submission[ 'bc-action' ] ) || $submission[ 'bc-action' ] !== self::ACTION ) {
			return false;
		}
		if ( empty( $submission[ '_wpnonce' ] ) || ! isset( $submission[ 'bc-profile' ][ 'user_id' ] ) ) {
			return false;
		}

		return true;
	}

	/**
	 * @param array    $submission
	 * @param \WP_User $user
	 *
	 * @return \WP_Error
	 */
	private function validate_submission( $submission, \WP_User $user ) {
		$errors = new \WP_Error();

		if ( ! wp_verify_nonce( $submission[ '_wpnonce' ], self::ACTION . $submission[ 'bc-profile' ][ 'user_id' ] ) ) {
			$errors->add( 'invalid_nonce', __( 'There was an error validating your request. Please try again.', 'bigcommerce' ) );
		}
		if ( $submission[ 'bc-profile' ][ 'user_id' ] != get_current_user_id() ) {
			$errors->add( 'invalid_user', __( 'There was an error validating your request. Please try again.', 'bigcommerce' ) );
		}

		if ( empty( $submission[ 'bc-profile' ][ 'first_name' ] ) ) {
			$errors->add( 'first_name', __( 'First Name is required.', 'bigcommerce' ) );
		}
		if ( empty( $submission[ 'bc-profile' ][ 'last_name' ] ) ) {
			$errors->add( 'last_name', __( 'Last Name is required.', 'bigcommerce' ) );
		}
		if ( empty( $submission[ 'bc-profile' ][ 'email' ] ) ) {
			$errors->add( 'email', __( 'Email Address is required.', 'bigcommerce' ) );
		} elseif ( ! is_email( $submission[ 'bc-profile' ][ 'email' ] ) ) {
			$errors->add( 'email', __( 'Please provide a valid email address.', 'bigcommerce' ) );
		}

		if ( ! empty( $submission[ 'bc-profile' ][ 'new_password' ] ) ) {
			if ( empty( $submission[ 'bc-profile' ][ 'current_password' ] ) ) {
				$errors->add( 'current_password', __( 'Current Password is required to update your password.', 'bigcommerce' ) );
			} elseif ( ! wp_check_password( $submission[ 'bc-profile' ][ 'current_password' ], $user->user_pass, $user->ID ) ) {
				$errors->add( 'current_password', __( 'The password you entered is incorrect.', 'bigcommerce' ) );
			}
			if ( empty( $submission[ 'bc-profile' ][ 'confirm_password' ] ) ) {
				$errors->add( 'confirm_password', __( 'Please confirm your requested password.', 'bigcommerce' ) );
			} elseif ( $submission[ 'bc-profile' ][ 'confirm_password' ] !== $submission[ 'bc-profile' ][ 'new_password' ] ) {
				$errors->add( 'new_password', __( 'Please check that you have typed your new password correctly.', 'bigcommerce' ) );
			}
		}

		/**
		 * Filters update profile form errors.
		 *
		 * @param \WP_Error $errors     WP error.
		 * @param array     $submission Submitted data.
		 */
		$errors = apply_filters( 'bigcommerce/form/update_profile/errors', $errors, $submission );

		return $errors;
	}

	private function get_profile( $submitted_profile ) {
		$defaults          = [
			'first_name' => '',
			'last_name'  => '',
			'company'    => '',
			'email'      => '',
			'phone'      => '',
		];
		$submitted_profile = array_filter( $submitted_profile, function ( $key ) use ( $defaults ) {
			return array_key_exists( $key, $defaults );
		}, ARRAY_FILTER_USE_KEY );

		$profile = wp_parse_args( $submitted_profile, $defaults );

		foreach ( $profile as $key => &$value ) {
			if ( $key === 'email' ) {
				$value = sanitize_email( $value );
			} else {
				$value = sanitize_text_field( $value );
			}
		}

		return $profile;
	}
}
