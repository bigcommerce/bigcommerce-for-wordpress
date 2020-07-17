<?php


namespace BigCommerce\Templates;


class Address_Form extends Controller {
	// Keys match those returned from the BigCommerce API

	const ADDRESS_ID = 'id';
	const FIRST_NAME = 'first_name';
	const LAST_NAME  = 'last_name';
	const COMPANY    = 'company';
	const STREET_1   = 'street_1';
	const STREET_2   = 'street_2';
	const CITY       = 'city';
	const STATE      = 'state';
	const ZIP        = 'zip';
	const COUNTRY    = 'country';
	const PHONE      = 'phone';

	const COUNTRIES = 'countries';
	const STATES    = 'states';
	const ERRORS    = 'errors';

	protected $template = 'components/accounts/address-form.php';


	protected function parse_options( array $options ) {
		$defaults = [
			self::ADDRESS_ID => 0,
			self::FIRST_NAME => '',
			self::LAST_NAME  => '',
			self::COMPANY    => '',
			self::STREET_1   => '',
			self::STREET_2   => '',
			self::CITY       => '',
			/**
			 * Filter the default state for address forms
			 *
			 * @param string $state The default state
			 */
			self::STATE      => apply_filters( 'bigcommerce/address/default_state', '' ),
			self::ZIP        => '',
			/**
			 * Filter the default country for address forms
			 *
			 * @param string $country The default country
			 */
			self::COUNTRY    => apply_filters( 'bigcommerce/address/default_country', 'United States' ),
			self::PHONE      => '',
			self::COUNTRIES  => [], // should follow the format given by \BigCommerce\Accounts\Countries::get_countries()
		];

		return wp_parse_args( $options, $defaults );
	}

	public function get_data() {
		$data = $this->options;
		list ( $data[ self::COUNTRIES ], $data[ self::STATES ] ) = $this->get_countries_and_states( $this->options[ self::COUNTRY ] );

		$error_data           = $this->get_error_data();
		$data[ self::ERRORS ] = $error_data ? $error_data[ 'error' ]->get_error_codes() : [];
		if ( $error_data && array_key_exists( 'submission', $error_data ) ) {
			if ( $error_data[ 'submission' ][ 'bc-address' ][ 'id' ] == $data[ self::ADDRESS_ID ] ) {
				$data = $this->restore_submission( $data, $error_data[ 'submission' ] );
			}
		}

		return $data;
	}

	protected function get_countries_and_states( $current_country ) {
		$countries = $this->options[ self::COUNTRIES ] ?: apply_filters( 'bigcommerce/countries/data', [] );
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
		if ( empty( $data[ 'submission' ][ 'bc-address' ] ) ) {
			return false;
		}

		if ( empty( $data[ 'submission' ][ 'bc-address' ][ 'id' ] ) ) {
			if ( empty( $this->options[ self::ADDRESS_ID ] ) ) {
				return $data;
			}

			return false;
		}

		if ( $data[ 'submission' ][ 'bc-address' ][ 'id' ] == $this->options[ self::ADDRESS_ID ] ) {
			return $data;
		}

		return false;
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
		$submission = array_key_exists( 'bc-address', $submission ) ? $submission[ 'bc-address' ] : [];
		$submission = array_intersect_key( $submission, $data ); // only keep keys that we already know about

		return array_merge( $data, $submission );
	}

}