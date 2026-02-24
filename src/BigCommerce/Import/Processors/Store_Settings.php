<?php


namespace BigCommerce\Import\Processors;


use Bigcommerce\Api\Client;
use BigCommerce\Api\Store_Api;
use BigCommerce\Api\v3\Api\SettingsApi;
use BigCommerce\Api\v3\ApiException;
use BigCommerce\Import\Runner\Status;
use BigCommerce\Logging\Error_Log;
use BigCommerce\Post_Types\Product\Product;
use BigCommerce\Settings;
use BigCommerce\Taxonomies\Channel\Channel;
use BigCommerce\Taxonomies\Channel\Connections;

class Store_Settings implements Import_Processor {

	const DOMAIN   = 'bigcommerce_domain';
	const MSF_FLAG = 'bigcommerce_multi_storefront';

	const PRODUCT_OUT_OF_STOCK = 'bigcommerce_product_out_of_stock_behavior';
	const OPTION_OUT_OF_STOCK  = 'bigcommerce_settings_option_out';

	const LEGACY_OPTIONS = [
		'product_out_of_stock_behavior',
		'option_out_of_stock_behavior'
	];
	/**
	 * @var Store_Api
	 */
	private $store_api;

	/**
	 * @var \BigCommerce\Import\Processors\Default_Customer_Group
	 */
	private $default_customer_group;

	/**
	 * @var \BigCommerce\Import\Processors\Storefront_Processor
	 */
	private $storefront_processor;

	/**
	 * @var \BigCommerce\Api\v3\Api\SettingsApi
	 */
	private $api_v3;

	public function __construct( Store_Api $store_api, Default_Customer_Group $default_customer_group, Storefront_Processor $storefront_processor, SettingsApi $api ) {
		$this->store_api              = $store_api;
		$this->api_v3                 = $api;
		$this->default_customer_group = $default_customer_group;
		$this->storefront_processor   = $storefront_processor;
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
				self::MSF_FLAG                                       => $store->features->multi_storefront_enabled,
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
				$connections  = new Connections();
				$channel      = $connections->current();
				$analytics    = $this->api_v3->getStoreAnalyticsSettings( (int) get_term_meta( $channel->term_id, Channel::CHANNEL_ID, true ));

				if ( ! empty( $analytics->data ) ) {
					$analytics                                                  = json_decode( json_encode( $analytics->data ), true );
					$settings[ Settings\Sections\Analytics::FACEBOOK_PIXEL ]   = $this->extract_facebook_pixel_id( $analytics );
					$settings[ Settings\Sections\Analytics::GOOGLE_ANALYTICS ] = $this->extract_google_analytics_id( $analytics );
				}

			}

			do_action( 'bigcommerce/log', Error_Log::DEBUG, __( 'Retrieved store settings', 'bigcommerce' ), [
				'settings' => $settings,
			] );

			foreach ( $settings as $key => $value ) {
				if ( $value !== false ) {
					update_option( $key, $value );
				}
			}

			$this->process_legacy_inventory_settings();

			// Save default customer group for MSF
			if ( $store->features->multi_storefront_enabled ) {
				$this->default_customer_group->run();
				$this->storefront_processor->run();
			}
			/**
			 * Hook after fetching currency code.
			 *
			 * @param string $currency_code The fetched currency code.
			 */
			do_action( 'bigcommerce/import/fetched_currency', $settings[ Settings\Sections\Currency::CURRENCY_CODE ] );
			/**
			 * Checks and updates routes after fetching store settings during an import.
			 *
			 * Triggers the `maybe_update_routes` method of the `Routes` class to ensure routes are updated
			 * after store settings are fetched during the import process.
			 */
			do_action( 'bigcommerce/import/fetched_store_settings', $settings );
		} catch ( \Exception $e ) {
			// if anything fails here, leave it be and let the user configure currency settings
			/**
			 * Hook when store settings cannot be fetched.
			 */
			do_action( 'bigcommerce/import/could_not_fetch_store_settings' );
		}

		$status->set_status( Status::FETCHED_STORE );
	}

	/**
	 * @return false|mixed|\stdClass|string
	 */
	private function get_legacy_inventory_settings() {
		try {
			$connection = $this->store_api->getConnection();

			return $connection->get( Client::$api_path . '/settings/inventory' );
		} catch ( \Exception $exception ) {
			do_action( 'bigcommerce/log', Error_Log::DEBUG, __( 'Could not retrieve legacy inventory settings', 'bigcommerce' ), [
				'message' => $exception->getMessage(),
				'trace'   => $exception->getTraceAsString(),
			] );

			return new \stdClass();
		}
	}

	/**
	 * Save store inventory settings
	 */
	private function process_legacy_inventory_settings() {
		$settings = $this->get_legacy_inventory_settings();

		if ( empty( $settings ) || ! is_object( $settings ) ) {
			do_action( 'bigcommerce/log', Error_Log::DEBUG, __( 'Legacy settings are empty. Continue import', 'bigcommerce' ), [] );

			return;
		}

		foreach ( $settings as $key => $setting ) {
			if ( ! in_array( $key, self::LEGACY_OPTIONS ) ) {
				continue;
			}

			update_option( sprintf( 'bigcommerce_%s', $key ), $setting );
		}

		do_action( 'bigcommerce/log', Error_Log::DEBUG, __( 'Legacy settings saved. Continue import', 'bigcommerce' ), [] );
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

	/**
	 * Is multi storefront is enabled
	 *
	 * @return bool
	 */
	public static function is_msf_on(): bool {
		return ( int ) get_option( Store_Settings::MSF_FLAG, 0 ) === 1;
	}
}
