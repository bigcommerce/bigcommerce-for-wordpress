<?php


namespace BigCommerce\Forms;


use BigCommerce\Accounts\Customer;
use BigCommerce\Accounts\Roles\Customer as Customer_Role;
use BigCommerce\Accounts\User_Profile_Settings;
use BigCommerce\Pages\Account_Page;

class Registration_Handler implements Form_Handler {

	const ACTION = 'register-account';

	public function handle_request( $submission ) {

		if ( ! $this->should_handle_request( $submission ) ) {
			return;
		}

		$errors = $this->validate_submission( $submission );


		$password = $submission[ 'bc-register' ][ 'new_password' ];
		// prevent logging sensitive information in plain text
		unset( $submission[ 'bc-register' ][ 'new_password' ] );
		unset( $submission[ 'bc-register' ][ 'confirm_password' ] );

		if ( count( $errors->get_error_codes() ) > 0 ) {

			/**
			 * Triggered when a form has errors that prevent completion.
			 *
			 * @param \WP_Error $errors     The message that will display to the user
			 * @param array     $submission The data submitted to the form
			 */
			do_action( 'bigcommerce/form/error', $errors, $submission );

			return;
		}

		$profile = $this->get_profile( $submission[ 'bc-register' ] );

		$address = $this->get_address( $submission[ 'bc-register' ] );

		// create the user
		// be sure to trim the email down to 60 characters to meet WP table limits for username
		$user_id = wp_create_user( mb_substr( $profile[ 'email' ], 0, 60 ), $password, $profile[ 'email' ] );

		if ( is_wp_error( $user_id ) ) {
			switch ( $user_id->get_error_code() ) {
				case 'existing_user_email':
					$errors->add( 'email', $user_id->get_error_message() );
					break;
				case 'existing_user_login':
					$errors->add( 'email', __( 'Sorry, that email address is already used!', 'bigcommerce' ) );
					break;
				case 'empty_user_login':
				case 'user_login_too_long':
				case 'invalid_username':
					$errors->add( 'email', __( 'Please verify that you have submitted a valid email address.', 'bigcommerce' ) );
					break;
				default:
					$errors->add( $user_id->get_error_code(), $user_id->get_error_message() );
					break;
			}
			do_action( 'bigcommerce/form/error', $errors, $submission );

			return;
		}


		/**
		 * Log the user in, automatically connecting the user with BigCommerce
		 * and setting up the profile with the submitted data
		 *
		 * @see \BigCommerce\Accounts\Login::connect_customer_id()
		 */
		$profile_filter = function ( $data ) use ( $profile, $password ) {
			$data = $profile;

			$data[ '_authentication' ][ 'password' ] = $password;

			return $data;
		};
		add_filter( 'bigcommerce/customer/create/args', $profile_filter, 10, 1 );

		wp_signon( [
			'user_login'    => $profile[ 'email' ],
			'user_password' => $password,
			'remember'      => true,
		] );

		remove_filter( 'bigcommerce/customer/create/args', $profile_filter, 10 );

		$user = new \WP_User( $user_id );

		/** This filter is documented in src/BigCommerce/Accounts/Login.php */
		$role = apply_filters( 'bigcommerce/user/default_role', Customer_Role::NAME );
		$user->set_role( $role );
		// all future password validation will be against the API for this user
		update_user_meta( $user_id, User_Profile_Settings::SYNC_PASSWORD, true );

		wp_set_current_user( $user_id );

		$customer = new Customer( $user_id );
		$customer->add_address( $address );

		$profile_page = get_option( Account_Page::NAME, 0 );

		$url = $profile_page ? get_permalink( $profile_page ) : home_url();

		/**
		 * The message to display when an account is created
		 *
		 * @param string $message
		 */
		$message = apply_filters( 'bigcommerce/form/registration/success_message', __( 'Account created!', 'bigcommerce' ) );

		/**
		 * Triggered when a form is successfully processed.
		 *
		 * @param string $message The message that will display to the user
		 * @param array  $submission The data submitted with the form
		 * @param string $url     The URL to redirect the user to
		 * @param array  $data    Optional data about the submission
		 */
		do_action( 'bigcommerce/form/success', $message, $submission, $url, [ 'key' => 'account_created' ] );
	}

	private function should_handle_request( $submission ) {
		if ( is_user_logged_in() ) {
			return false;
		}
		if ( ! get_option( 'users_can_register' ) ) {
			return false;
		}
		if ( empty( $submission[ 'bc-action' ] ) || $submission[ 'bc-action' ] !== self::ACTION ) {
			return false;
		}
		if ( empty( $submission[ '_wpnonce' ] ) || empty( $submission[ 'bc-register' ] ) ) {
			return false;
		}

		return true;
	}

	private function validate_submission( $submission ) {
		$errors = new \WP_Error();

		if ( ! wp_verify_nonce( $submission[ '_wpnonce' ], self::ACTION ) ) {
			$errors->add( 'invalid_nonce', __( 'There was an error validating your request. Please try again.', 'bigcommerce' ) );
		}

		// Required for customer creation
		if ( empty( $submission[ 'bc-register' ][ 'first_name' ] ) ) {
			$errors->add( 'first_name', __( 'First Name is required.', 'bigcommerce' ) );
		}
		if ( empty( $submission[ 'bc-register' ][ 'last_name' ] ) ) {
			$errors->add( 'last_name', __( 'Last Name is required.', 'bigcommerce' ) );
		}
		if ( empty( $submission[ 'bc-register' ][ 'email' ] ) ) {
			$errors->add( 'email', __( 'Email Address is required.', 'bigcommerce' ) );
		} elseif ( ! is_email( $submission[ 'bc-register' ][ 'email' ] ) ) {
			$errors->add( 'email', __( 'Please verify that you have submitted a valid email address.', 'bigcommerce' ) );
		}
		if ( empty( $submission[ 'bc-register' ][ 'new_password' ] ) ) {
			$errors->add( 'new_password', __( 'Please set your password.', 'bigcommerce' ) );
		}
		if ( empty( $submission[ 'bc-register' ][ 'confirm_password' ] ) ) {
			$errors->add( 'confirm_password', __( 'Please confirm your password.', 'bigcommerce' ) );
		} elseif ( $submission[ 'bc-register' ][ 'new_password' ] !== $submission[ 'bc-register' ][ 'confirm_password' ] ) {
			$errors->add( 'confirm_password', __( 'Passwords do not match.', 'bigcommerce' ) );
		}

		// Required for address creation
		if ( empty( $submission[ 'bc-register' ][ 'phone' ] ) ) {
			$errors->add( 'phone', __( 'Phone number is required.', 'bigcommerce' ) );
		}
		if ( empty( $submission[ 'bc-register' ][ 'street_1' ] ) ) {
			$errors->add( 'street_1', __( 'Address Line 1 is required.', 'bigcommerce' ) );
		}
		if ( empty( $submission[ 'bc-register' ][ 'city' ] ) ) {
			$errors->add( 'city', __( 'City is required.', 'bigcommerce' ) );
		}
		if ( empty( $submission[ 'bc-register' ][ 'state' ] ) ) {
			$errors->add( 'state', __( 'State/Province is required.', 'bigcommerce' ) );
		}
		if ( empty( $submission[ 'bc-register' ][ 'zip' ] ) ) {
			$errors->add( 'zip', __( 'Zip/Postcode is required.', 'bigcommerce' ) );
		}
		if ( empty( $submission[ 'bc-register' ][ 'country' ] ) ) {
			$errors->add( 'country', __( 'Country is required.', 'bigcommerce' ) );
		}

		$errors = apply_filters( 'bigcommerce/form/registration/errors', $errors, $submission );

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

	private function get_address( $submitted_address ) {
		$defaults          = [
			'first_name' => '',
			'last_name'  => '',
			'company'    => '',
			'street_1'   => '',
			'street_2'   => '',
			'city'       => '',
			'state'      => '',
			'zip'        => '',
			'country'    => '',
			'phone'      => '',
		];
		$submitted_address = array_filter( $submitted_address, function ( $key ) use ( $defaults ) {
			return array_key_exists( $key, $defaults );
		}, ARRAY_FILTER_USE_KEY );
		$address           = wp_parse_args( $submitted_address, $defaults );
		$address = array_map( 'sanitize_text_field', $address );

		return $address;
	}
}