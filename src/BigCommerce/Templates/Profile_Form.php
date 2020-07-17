<?php


namespace BigCommerce\Templates;


use BigCommerce\Accounts\Customer;

class Profile_Form extends Controller {
	const USER_ID    = 'user_id';
	const FIRST_NAME = 'first_name';
	const LAST_NAME  = 'last_name';
	const COMPANY    = 'company';
	const EMAIL      = 'email';
	const PHONE      = 'phone';
	const ERRORS     = 'errors';

	protected $template = 'components/accounts/profile-form.php';

	protected function parse_options( array $options ) {
		$defaults = [
			self::USER_ID => get_current_user_id(),
		];

		return wp_parse_args( $options, $defaults );
	}

	public function get_data() {
		$customer   = new Customer( $this->options[ self::USER_ID ] );
		$error_data = $this->get_error_data();

		$data = $customer->get_profile();

		$data[ self::USER_ID ] = $this->options[ self::USER_ID ];
		$data[ self::ERRORS ]  = $error_data ? $error_data[ 'error' ]->get_error_codes() : [];

		if ( $error_data && array_key_exists( 'submission', $error_data ) ) {
			$data = $this->restore_submission( $data, $error_data[ 'submission' ] );
		}

		return $data;
	}

	private function get_error_data() {
		if ( empty( $_REQUEST[ 'bc-error' ] ) ) {
			return false;
		}

		$bc_error = filter_var_array( $_REQUEST, [ 'bc-error' => FILTER_SANITIZE_STRING ] );
		$data     = get_transient( $bc_error[ 'bc-error' ] );
		if ( empty( $data[ 'error' ] ) || ! array_key_exists( 'user_id', $data ) ) {
			return false;
		}
		if ( $data[ 'user_id' ] != get_current_user_id() ) {
			return false;
		}
		if ( ! is_wp_error( $data[ 'error' ] ) || count( $data[ 'error' ]->get_error_codes() ) < 1 ) {
			return false;
		}

		return $data;
	}

	/**
	 * If the user has submitted the form, restore their submission
	 * so they don't have to re-type everything
	 *
	 * @param array $data
	 * @param array $submission
	 *
	 * @return array
	 */
	private function restore_submission( $data, $submission ) {
		$submission = array_key_exists( 'bc-profile', $submission ) ? $submission[ 'bc-profile' ] : [];
		$submission = array_intersect_key( $submission, $data ); // only keep keys that we already know about

		return array_merge( $data, $submission );
	}
}