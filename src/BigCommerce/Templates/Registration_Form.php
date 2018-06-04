<?php


namespace BigCommerce\Templates;


class Registration_Form extends Controller {
	const DEFAULTS  = 'defaults';
	const STATES    = 'states';
	const COUNTRIES = 'countries';
	const ERRORS    = 'errors';

	protected $template = 'components/registration-form.php';

	protected function parse_options( array $options ) {
		return [];
	}

	public function get_data() {
		$data = [
			self::DEFAULTS => $this->get_form_defaults(),
		];
		list ( $data[ self::COUNTRIES ], $data[ self::STATES ] ) = $this->get_countries_and_states( $data[ self::DEFAULTS ][ 'country' ] );

		$error_data           = $this->get_error_data();
		$data[ self::ERRORS ] = $error_data ? $error_data[ 'error' ]->get_error_codes() : [];
		if ( $error_data && array_key_exists( 'submission', $error_data ) ) {
			$data[ self::DEFAULTS ] = $this->restore_submission( $data[ self::DEFAULTS ], $error_data[ 'submission' ] );
		}
		return $data;
	}

	protected function get_form_defaults() {
		return [
			'email'      => '',
			'password'   => '',
			'first_name' => '',
			'last_name'  => '',
			'street_1'   => '',
			'street_2'   => '',
			'company'    => '',
			'city'       => '',
			/**
			 * This filter is documented in src/BigCommerce/Templates/Address_Form.php
			 */
			'state'      => apply_filters( 'bigcommerce/address/default_state', '' ),
			/**
			 * This filter is documented in src/BigCommerce/Templates/Address_Form.php
			 */
			'country'    => apply_filters( 'bigcommerce/address/default_country', 'United States' ),
			'zip'        => '',
			'phone'      => '',
		];
	}

	protected function get_countries_and_states( $current_country ) {
		$countries = apply_filters( 'bigcommerce/countries/data', [] );
		foreach ( $countries as $country ) {
			if ( $country->country == $current_country ) {
				$states = $country->states;
				break;
			}
		}
		$countries = wp_list_pluck( $countries, 'country', 'country_iso3' );
		asort( $countries );
		$states = is_array( $states ) ? wp_list_pluck( $states, 'state', 'state_abbreviation' ) : [];
		asort( $states );

		return [ $countries, $states ];
	}

	private function get_error_data() {
		if ( empty( $_REQUEST[ 'bc-error' ] ) ) {
			return false;
		}

		$data = get_transient( $_REQUEST[ 'bc-error' ] );
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
		$submission = array_key_exists( 'bc-register', $submission ) ? $submission[ 'bc-register' ] : [];
		$submission = array_intersect_key( $submission, $data ); // only keep keys that we already know about

		return array_merge( $data, $submission );
	}
}