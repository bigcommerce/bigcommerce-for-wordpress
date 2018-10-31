<?php


namespace BigCommerce\Templates;


class Registration_Form extends Form_Controller {
	const STATES    = 'states';
	const COUNTRIES = 'countries';

	protected $template = 'components/accounts/registration-form.php';

	protected $submission_key = 'bc-register';

	protected function parse_options( array $options ) {
		return [];
	}

	public function get_data() {
		$data = parent::get_data();
		list ( $data[ self::COUNTRIES ], $data[ self::STATES ] ) = $this->get_countries_and_states( $data[ self::DEFAULTS ][ 'country' ] );
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
}