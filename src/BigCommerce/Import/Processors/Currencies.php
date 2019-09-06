<?php


namespace BigCommerce\Import\Processors;

use BigCommerce\Api\Currencies_Api;
use BigCommerce\Api\v3\ApiException;
use BigCommerce\Import\Runner\Status;
use BigCommerce\Logging\Error_Log;
use BigCommerce\Settings;

class Currencies implements Import_Processor {
	/** @var Currencies_Api */
	private $currencies_api;

	public function __construct( Currencies_Api $currencies_api ) {
		$this->currencies_api = $currencies_api;
	}

	public function run() {
		$status = new Status();
		$status->set_status( Status::FETCHING_CURRENCIES );

		try {
			do_action( 'bigcommerce/log', Error_Log::DEBUG, __( 'Requesting currency settings', 'bigcommerce' ), [] );

			$currencies = $this->currencies_api->get_currencies();

			if ( empty( $currencies ) ) { // v2 api client doesn't throw proper errors
				throw new ApiException( __( 'Unable to retrieve currency information', 'bigcommerce' ) );
			}

			$currencies = array_filter( $currencies, function ( $currency ) {
				return ! empty( $currency['enabled'] );
			} );
			$currencies = array_combine( wp_list_pluck( $currencies, 'currency_code' ), $currencies );

			update_option( Settings\Sections\Currency::ENABLED_CURRENCIES, $currencies );

			do_action( 'bigcommerce/import/fetched_currencies', $currencies );
		} catch ( \Exception $e ) {
			do_action( 'bigcommerce/import/could_not_fetch_currency_settings', $e );
		}

		$status->set_status( Status::FETCHED_CURRENCIES );
	}
}
