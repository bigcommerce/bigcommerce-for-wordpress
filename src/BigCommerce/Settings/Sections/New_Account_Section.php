<?php


namespace BigCommerce\Settings\Sections;

use BigCommerce\Settings\Screens\Create_Account_Screen;

class New_Account_Section extends Settings_Section {
	const NAME       = 'bc_create_account';
	const STORE_INFO = 'bigcommerce_new_store_info';

	public function register_settings_section() {
		add_settings_section(
			self::NAME,
			__( 'Create an Account', 'bigcommerce' ),
			'__return_empty_string',
			Create_Account_Screen::NAME
		);

		register_setting(
			self::NAME,
			self::STORE_INFO
		);

		$user = wp_get_current_user();

		$fields = [
			'store_name' => [
				'label'   => __( 'Store Name', 'bigcommerce' ),
				'default' => get_option( 'blogname' ),
			],
			'email'      => [
				'label'   => __( 'Email Address', 'bigcommerce' ),
				'default' => $user->user_email,
			],
			'first_name' => [
				'label'   => __( 'First Name', 'bigcommerce' ),
				'default' => $user->first_name,
			],
			'last_name'  => [
				'label'   => __( 'Last Name', 'bigcommerce' ),
				'default' => $user->last_name,
			],
			'street_1'   => [
				'label' => __( 'Address 1', 'bigcommerce' ),
			],
			'street_2'   => [
				'label'    => __( 'Address 2', 'bigcommerce' ),
				'required' => false,
			],
			'city'       => [
				'label' => __( 'City', 'bigcommerce' ),
			],
			'state'      => [
				'label'    => __( 'State', 'bigcommerce' ),
				'callback' => [ $this, 'render_state_field' ],
			],
			'zip'        => [
				'label' => __( 'ZIP/Postcode', 'bigcommerce' ),
			],
			'country'    => [
				'label'    => __( 'Country', 'bigcommerce' ),
				'callback' => [ $this, 'render_country_field' ],
			],
			'phone'      => [
				'label' => __( 'Phone', 'bigcommerce' ),
			],
		];

		foreach ( $fields as $key => $field ) {
			$required_label = ! isset( $field[ 'required' ] ) ? sprintf( '<span class="bc-settings-field--required">*<span>' ) : '';
			add_settings_field(
				self::STORE_INFO . '-' . $key,
				$field[ 'label' ] . $required_label,
				empty( $field[ 'callback' ] ) ? [ $this, 'render_field' ] : $field[ 'callback' ],
				Create_Account_Screen::NAME,
				self::NAME,
				[
					'option'    => $key,
					'default'   => empty( $field[ 'default' ] ) ? '' : $field[ 'default' ],
					'required'  => isset( $field[ 'required' ] ) ? (bool) $field[ 'required' ] : true,
					'label_for' => 'field-' . self::STORE_INFO . '-' . $key,
				]
			);
		}

	}

	/**
	 * @param array $args
	 *
	 * @return void
	 */
	public function render_field( $args ) {
		$submission = get_option( Create_Account_Screen::SUBMITTED_DATA, [] );
		$option     = $args[ 'option' ];
		$default    = isset( $args[ 'default' ] ) ? $args[ 'default' ] : '';
		$type       = 'text';
		if ( ! empty( $submission[ self::STORE_INFO ][ $option ] ) ) {
			$value = $submission[ self::STORE_INFO ][ $option ];
		} else {
			$value = $default;
		}
		$placeholder = ( isset( $args[ 'required' ] ) && $args[ 'required' ] === false ) ? sprintf( 'placeholder="%s"', esc_attr( __( 'Optional', 'bigcommerce' ) ) ) : '';
		printf(
			'<input id="field-%s-%s" type="%s" value="%s" class="regular-text code" name="%s[%s]" %s data-lpignore="true" />',
			esc_attr( self::STORE_INFO ),
			esc_attr( $option ),
			esc_attr( $type ),
			esc_attr( $value ),
			esc_attr( self::STORE_INFO ),
			esc_attr( $option ),
			$placeholder
		);
		if ( ! empty( $args[ 'description' ] ) ) {
			printf( '<p class="description">%s</p>', esc_html( $args[ 'description' ] ) );
		}
	}

	public function render_state_field( $args ) {
		$submission = get_option( Create_Account_Screen::SUBMITTED_DATA, [] );
		$option     = $args[ 'option' ];
		/**
		 * This filter is documented in src/BigCommerce/Templates/Address_Form.php
		 */
		$args[ 'default' ] = apply_filters( 'bigcommerce/address/default_state', '' );
		/**
		 * This filter is documented in src/BigCommerce/Templates/Address_Form.php
		 */
		$country = apply_filters( 'bigcommerce/address/default_country', 'United States' );
		if ( ! empty( $submission[ self::STORE_INFO ][ 'country' ] ) ) {
			$country = $submission[ self::STORE_INFO ][ $option ];
		}
		list( $countries, $states ) = $this->get_countries_and_states( $country );

		if ( ! empty( $submission[ self::STORE_INFO ][ $option ] ) ) {
			$value = $submission[ self::STORE_INFO ][ $option ];
		} else {
			$value = $args[ 'default' ];
		}
		if ( empty( $states ) ) {
			printf( '<input id="field-%s-%s" type="text" name="%s[%s]" data-js="bc-dynamic-state-control" value="%s" class="regular-text code" data-lpignore="true" />', esc_attr( self::STORE_INFO ), esc_attr( $option ), esc_attr( self::STORE_INFO ), esc_attr( $option ), esc_attr( $value ) );
		} else {
			printf( '<select id="field-%s-%s" name="%s[%s]" data-js="bc-dynamic-state-control">', esc_attr( self::STORE_INFO ), esc_attr( $option ), esc_attr( self::STORE_INFO ), esc_attr( $option ) );
			foreach ( $states as $state_abbr => $state_name ) {
				printf( '<option value="%s" data-state-abbr="%s" %s>%s</option>', esc_attr( $state_abbr ), esc_attr( $state_abbr ), selected( $value, $state_abbr, false ), esc_html( $state_name ) );
			}
			echo '</select>';
		}
	}

	public function render_country_field( $args ) {
		$submission = get_option( Create_Account_Screen::SUBMITTED_DATA, [] );
		$option     = $args[ 'option' ];
		/**
		 * This filter is documented in src/BigCommerce/Templates/Address_Form.php
		 */
		$default_country = apply_filters( 'bigcommerce/address/default_country', 'United States' );
		list( $countries, $states ) = $this->get_countries_and_states( $default_country );
		if ( empty( $countries ) ) {
			$this->render_field( $args );

			return;
		}
		if ( ! empty( $submission[ self::STORE_INFO ][ $option ] ) ) {
			$value = $submission[ self::STORE_INFO ][ $option ];
		} else {
			$value = $default_country;
		}
		printf( '<select id="field-%s-%s" name="%s[%s]" data-js="bc-dynamic-country-select">', esc_attr( self::STORE_INFO ), esc_attr( $option ), esc_attr( self::STORE_INFO ), esc_attr( $option ) );
		foreach ( $countries as $iso => $country_name ) {
			$selected = false;
			if ( $value === $iso || $value === $country_name ) {
				$selected = true;
			}
			printf( '<option value="%s" data-country-iso="%s" %s>%s</option>', esc_attr( $iso ), esc_attr( $iso ), selected( $selected, true, false ), esc_html( $country_name ) );
		}
		echo '</select>';
	}


	protected function get_countries_and_states( $current_country ) {
		$countries = apply_filters( 'bigcommerce/countries/data', [] );
		$states    = [];
		foreach ( $countries as $country ) {
			if ( $country->country == $current_country ) {
				$states = $country->states;
				break;
			}
		}
		$countries = wp_list_pluck( $countries, 'country', 'country_iso2' );
		asort( $countries );
		$states = is_array( $states ) ? wp_list_pluck( $states, 'state', 'state_abbreviation' ) : [];
		asort( $states );

		return [ $countries, $states ];
	}

	/**
	 * @param array     $submission The data submitted to the form
	 * @param \WP_Error $errors
	 *
	 * @return void
	 * @action bigcommerce/create_account/validate_request
	 */
	public function validate_request( $submission, $errors ) {
		$store_info = $submission[ self::STORE_INFO ];

		if ( empty( $store_info[ 'store_name' ] ) ) {
			$errors->add( 'store_name', __( 'Store Name is required.', 'bigcommerce' ) );
		}
		if ( empty( $store_info[ 'email' ] ) ) {
			$errors->add( 'email', __( 'Email Address is required.', 'bigcommerce' ) );
		} elseif ( ! is_email( $store_info[ 'email' ] ) ) {
			$errors->add( 'email', __( 'Please verify that you have submitted a valid email address.', 'bigcommerce' ) );
		}
		if ( empty( $store_info[ 'first_name' ] ) ) {
			$errors->add( 'first_name', __( 'First Name is required.', 'bigcommerce' ) );
		}
		if ( empty( $store_info[ 'last_name' ] ) ) {
			$errors->add( 'last_name', __( 'Last Name is required.', 'bigcommerce' ) );
		}
		if ( empty( $store_info[ 'street_1' ] ) ) {
			$errors->add( 'street_1', __( 'Address Line 1 is required.', 'bigcommerce' ) );
		}
		if ( empty( $store_info[ 'city' ] ) ) {
			$errors->add( 'city', __( 'City is required.', 'bigcommerce' ) );
		}
		if ( empty( $store_info[ 'state' ] ) ) {
			$errors->add( 'state', __( 'State/Province is required.', 'bigcommerce' ) );
		}
		if ( empty( $store_info[ 'zip' ] ) ) {
			$errors->add( 'zip', __( 'Zip/Postcode is required.', 'bigcommerce' ) );
		}
		if ( empty( $store_info[ 'country' ] ) ) {
			$errors->add( 'country', __( 'Country is required.', 'bigcommerce' ) );
		}
		if ( empty( $store_info[ 'phone' ] ) ) {
			$errors->add( 'phone', __( 'Phone number is required.', 'bigcommerce' ) );
		}
	}
}