<?php


namespace BigCommerce\Import\Processors;


use BigCommerce\Api\Store_Api;
use BigCommerce\Api\v3\ApiException;
use BigCommerce\Api\v3\Model\CartRequestData;
use BigCommerce\Import\Runner\Status;
use BigCommerce\Settings;

class Store_Settings implements Import_Processor {
	/**
	 * @var Store_Api
	 */
	private $store_api;

	public function __construct( Store_Api $store_api ) {
		$this->store_api = $store_api;
	}

	public function run() {
		$status = new Status();
		$status->set_status( Status::FETCHING_STORE );

		try {
			$store     = $this->store_api->getStore();
			$analytics = $this->store_api->get_analytics_settings();

			if ( empty( $store ) ) { // v2 api client doesn't throw proper errors
				throw new ApiException( __( 'Unable to retrieve store information', 'bigcommerce' ) );
			}

			$settings = [
				Settings\Currency::CURRENCY_CODE            => $store->currency,
				Settings\Currency::CURRENCY_SYMBOL          => $store->currency_symbol,
				Settings\Currency::CURRENCY_SYMBOL_POSITION => $this->sanitize_currency_symbol_position( $store->currency_symbol_location ),
				Settings\Currency::DECIMAL_UNITS            => absint( $store->decimal_places ),

				Settings\Units::MASS   => $this->sanitize_mass_unit( $store->weight_units ),
				Settings\Units::LENGTH => $this->sanitize_length_unit( $store->dimension_units ),

				Settings\Analytics::FACEBOOK_PIXEL   => $this->extract_facebook_pixel_id( $analytics ),
				Settings\Analytics::GOOGLE_ANALYTICS => $this->extract_google_analytics_id( $analytics ),
			];

			foreach ( $settings as $key => $value ) {
				if ( $value !== false ) {
					update_option( $key, $value );
				}
			}
			do_action( 'bigcommerce/import/fetched_currency', $settings[ Settings\Currency::CURRENCY_CODE ] );
			do_action( 'bigcommerce/import/fetched_store_settings', $settings );
		} catch ( \Exception $e ) {
			// if anything fails here, leave it be and let the user configure currency settings
			do_action( 'bigcommerce/import/could_not_fetch_store_settings' );
		}

		$status->set_status( Status::FETCHED_STORE );
	}

	private function sanitize_currency_symbol_position( $position ) {
		$values = [
			Settings\Currency::POSITION_LEFT,
			Settings\Currency::POSITION_RIGHT,
		];

		return ( in_array( $position, $values ) ? $position : false );
	}

	private function sanitize_mass_unit( $unit ) {
		switch ( strtolower( $unit ) ) {
			case 'lbs':
				return Settings\Units::POUND;
			case 'ounces':
				return Settings\Units::OUNCE;
			case 'kgs':
				return Settings\Units::KILOGRAM;
			case 'grams':
				return Settings\Units::GRAM;
			case 'tonnes':
				return Settings\Units::TONNE;
			default:
				return false;
		}
	}

	private function sanitize_length_unit( $unit ) {
		switch ( strtolower( $unit ) ) {
			case 'inches':
				return Settings\Units::INCH;
			case 'centimeters':
				return Settings\Units::CENTIMETER;
			default:
				return false;
		}
	}

	private function extract_facebook_pixel_id( $settings ) {
		return $this->extract_analytics_code( $settings, 'Facebook Pixel' );
	}

	private function extract_google_analytics_id( $settings ) {
		$code = $this->extract_analytics_code( $settings, 'Google Analytics' );
		// extract the analytics ID from the tracking code
		if ( ! preg_match( '/ua-\d{4,9}-\d{1,4}/i', $code, $matches ) ) {
			return '';
		}

		return $matches[ 0 ];
	}

	private function extract_analytics_code( $settings, $name ) {
		if ( empty( $settings ) || ! is_array( $settings ) ) {
			return '';
		}
		foreach ( $settings as $account ) {
			if ( $account[ 'name' ] == $name ) {
				return $account[ 'code' ];
			}
		}

		return '';
	}

}