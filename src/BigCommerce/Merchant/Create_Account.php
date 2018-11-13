<?php


namespace BigCommerce\Merchant;


use BigCommerce\Merchant\Models\Account_Contact;
use BigCommerce\Merchant\Models\Create_Account_Request;

class Create_Account {

	/**
	 * @var Onboarding_Api
	 */
	private $onboarding;

	/**
	 * @param Onboarding_Api $onboarding_api
	 */
	public function __construct( Onboarding_Api $onboarding_api ) {
		$this->onboarding = $onboarding_api;
	}

	/**
	 * @param array     $submission The data submitted to the new account form
	 * @param \WP_Error $errors     Error object to collect any errors that occur
	 *
	 * @return void
	 * @action bigcommerce/create_account/submit_request
	 */
	public function request_account( $submission, $errors ) {
		$secret_key = wp_generate_password( 32, true, true );
		update_option( Onboarding_Api::AUTH_KEY, $secret_key, false );

		$contact = new Account_Contact([
			'email'          => sanitize_email( $submission[ 'email' ] ),
			'first_name'     => sanitize_text_field( $submission[ 'first_name' ] ),
			'last_name'      => sanitize_text_field( $submission[ 'last_name' ] ),
			'address_line_1' => sanitize_text_field( $submission[ 'street_1' ] ),
			'address_line_2' => sanitize_text_field( $submission[ 'street_2' ] ),
			'city'           => sanitize_text_field( $submission[ 'city' ] ),
			'district'       => sanitize_text_field( $submission[ 'state' ] ),
			'postal_code'    => sanitize_text_field( $submission[ 'zip' ] ),
			'country'        => sanitize_text_field( $submission[ 'country' ] ),
			'phone_number'   => sanitize_text_field( $submission[ 'phone' ] ),
		]);
		$request = new Create_Account_Request( $secret_key, sanitize_text_field( $submission[ 'store_name' ] ), home_url( '/' ), $contact );
		$response = $this->onboarding->create_account( $request );

		if ( is_wp_error( $response ) ) {
			$errors->add( $response->get_error_code(), $response->get_error_message(), $response->get_error_data() );

			return;
		}

		$response_code = (int) wp_remote_retrieve_response_code( $response );
		if ( $response_code < 200 || $response_code >= 400 ) {

			$this->handle_error_response( $response, $errors );

			return;
		}

		$response_body = json_decode( wp_remote_retrieve_body( $response ), true );
		if ( empty( $response_body[ 'account_id' ] ) || empty( $response_body[ 'store_id' ] ) ) {
			$errors->add( 'invalid_response', __( 'We encountered an unexpected error creating your account. Please try again.' ), $response );

			return;
		}

		update_option( Onboarding_Api::ACCOUNT_ID, $response_body[ 'account_id' ] );
		update_option( Onboarding_Api::STORE_ID, $response_body[ 'store_id' ] );

		return;
	}

	/**
	 * Add errors from the response to the error accumulator
	 *
	 * @param array     $response
	 * @param \WP_Error $errors
	 *
	 * @return void
	 */
	private function handle_error_response( $response, $errors ) {
		switch ( (int) wp_remote_retrieve_response_code( $response ) ) {
			case 409:
				$errors->add( 'already_exists', __( 'An account already exists with your email address. Try connecting to your existing account.', 'bigcommerce' ), $response );
				break;
			case 422:
				$response_body = json_decode( wp_remote_retrieve_body( $response ), true );
				if ( ! empty( $response_body[ 'errors' ] ) ) {
					foreach ( $response_body[ 'errors' ] as $error_key => $error_value ) {
						$errors->add( $error_key, sprintf( __( 'Validation error: %s', 'bigcommerce' ), $error_value ), $response );
					}
				} else {
					$errors->add( 'api_error', __( 'There was a problem creating your account. Please verify your information and try again.', 'bigcommerce' ), $response );
				}
				break;
			default:
				$errors->add( 'api_error', __( 'There was a problem creating your account. Please try again.', 'bigcommerce' ), $response );
				break;
		}
	}
}