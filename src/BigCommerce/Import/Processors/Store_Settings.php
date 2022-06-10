<?php


namespace BigCommerce\Import\Processors;


use BigCommerce\Api\Store_Api;
use BigCommerce\Api\v3\ApiException;
use BigCommerce\Import\Runner\Status;
use BigCommerce\Logging\Error_Log;
use BigCommerce\Post_Types\Product\Product;
use BigCommerce\Settings;

class Store_Settings implements Import_Processor {

	const DOMAIN = 'bigcommerce_domain';

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

			do_action( 'bigcommerce/log', Error_Log::DEBUG, __( 'Requesting store settings', 'bigcommerce' ), [] );

			$store = $this->store_api->getStore();

			if ( empty( $store ) ) { // v2 api client doesn't throw proper errors
				throw new ApiException( __( 'Unable to retrieve store information', 'bigcommerce' ) );
			}

			$settings = [
				self::DOMAIN                                         => $store->domain,
				Settings\Sections\Currency::CURRENCY_CODE            => $store->currency,
				Settings\Sections\Currency::CURRENCY_SYMBOL          => $store->currency_symbol,
				Settings\Sections\Currency::CURRENCY_SYMBOL_POSITION => $this->sanitize_currency_symbol_position( $store->currency_symbol_location ),
				Settings\Sections\Currency::DECIMAL_UNITS            => absint( $store->decimal_places ),
				Settings\Sections\Currency::INTEGER_UNITS            => $this->get_max_integer_units(),

				Settings\Sections\Units::MASS   => $this->sanitize_mass_unit( $store->weight_units ),
				Settings\Sections\Units::LENGTH => $this->sanitize_length_unit( $store->dimension_units ),

				Settings\Sections\Wishlists::ENABLED => isset( $store->features->wishlists_enabled ) ? (int) $store->features->wishlists_enabled : 0,
			];

			if ( get_option( Settings\Sections\Analytics::SYNC_ANALYTICS, 1 ) ) {
				$analytics                                                 = $this->store_api->get_analytics_settings();
				$settings[ Settings\Sections\Analytics::FACEBOOK_PIXEL ]   = $this->extract_facebook_pixel_id( $analytics );
				$settings[ Settings\Sections\Analytics::GOOGLE_ANALYTICS ] = $this->extract_google_analytics_id( $analytics );
			}

			do_action( 'bigcommerce/log', Error_Log::DEBUG, __( 'Retrieved store settings', 'bigcommerce' ), [
				'settings' => $settings,
			] );

			foreach ( $settings as $key => $value ) {
				if ( $value !== false ) {
					update_option( $key, $value );
				}
			}
			do_action( 'bigcommerce/import/fetched_currency', $settings[ Settings\Sections\Currency::CURRENCY_CODE ] );
			do_action( 'bigcommerce/import/fetched_store_settings', $settings );
		} catch ( \Exception $e ) {
			// if anything fails here, leave it be and let the user configure currency settings
			do_action( 'bigcommerce/import/could_not_fetch_store_settings' );
		}

		$status->set_status( Status::FETCHED_STORE );
	}

	private function sanitize_currency_symbol_position( $position ) {
		$values = [
			Settings\Sections\Currency::POSITION_LEFT,
			Settings\Sections\Currency::POSITION_RIGHT,
		];

		return ( in_array( $position, $values ) ? $position : false );
	}

	private function sanitize_mass_unit( $unit ) {
		switch ( strtolower( $unit ) ) {
			case 'lbs':
				return Settings\Sections\Units::POUND;
			case 'ounces':
				return Settings\Sections\Units::OUNCE;
			case 'kgs':
				return Settings\Sections\Units::KILOGRAM;
			case 'grams':
				return Settings\Sections\Units::GRAM;
			case 'tonnes':
				return Settings\Sections\Units::TONNE;
			default:
				return false;
		}
	}

	private function sanitize_length_unit( $unit ) {
		switch ( strtolower( $unit ) ) {
			case 'inches':
				return Settings\Sections\Units::INCH;
			case 'centimeters':
				return Settings\Sections\Units::CENTIMETER;
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

		return $matches[0];
	}

	private function extract_analytics_code( $settings, $name ) {
		if ( empty( $settings ) || ! is_array( $settings ) ) {
			return '';
		}
		foreach ( $settings as $account ) {
			if ( $account['name'] == $name ) {
				return $account['code'];
			}
		}

		return '';
	}

	/**
	 * Get the max price integer part length to be used in the ordering by price
	 *
	 * @return int
	 */
	private function get_max_integer_units() {
		global $wpdb;
		$max_length = $wpdb->get_var( $wpdb->prepare(
			"SELECT CHAR_LENGTH( MAX( FLOOR( meta_value ) ) ) AS max_length FROM {$wpdb->postmeta} WHERE meta_key=%s",
			Product::PRICE_META_KEY
		) );

		return min( (int) $max_length, 4 );
	}

}
