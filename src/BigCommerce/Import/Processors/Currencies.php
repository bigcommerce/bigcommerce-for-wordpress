<?php


namespace BigCommerce\Import\Processors;

use BigCommerce\Api\Currencies_Api;
use BigCommerce\Api\v3\Api\CurrencyApi;
use BigCommerce\Api\v3\ApiException;
use BigCommerce\Import\Runner\Status;
use BigCommerce\Logging\Error_Log;
use BigCommerce\Settings;
use BigCommerce\Taxonomies\Channel\Channel;
use BigCommerce\Taxonomies\Channel\Connections;

class Currencies implements Import_Processor {
	/** @var Currencies_Api */
	private $currencies_api;

	/** @var CurrencyApi */
	private $currencies_v3_api;
	/**
	 * @var \BigCommerce\Taxonomies\Channel\Connections
	 */
	private $connections;

	public function __construct( Currencies_Api $currencies_api, CurrencyApi $currencyV3, Connections $connections ) {
		$this->currencies_api = $currencies_api;
		$this->currencies_v3_api = $currencyV3;
		$this->connections = $connections;
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
			$this->process_channel_currencies();
			do_action( 'bigcommerce/import/fetched_currencies', $currencies );
		} catch ( \Exception $e ) {
			do_action( 'bigcommerce/import/could_not_fetch_currency_settings', $e );
		}

		$status->set_status( Status::FETCHED_CURRENCIES );
	}

	/**
	 * Retrieve currencies set for each channel and store in term meta
	 */
	public function process_channel_currencies(): void {
		// Should be performed only for MSF stores
		if ( ! Store_Settings::is_msf_on() ) {
			return;
		}

		$channels = $this->connections->active();
		do_action( 'bigcommerce/log', Error_Log::DEBUG, __( 'Requesting currency assignments for channels', 'bigcommerce' ), [] );
		foreach ( $channels as $channel ) {
			$channel_id = get_term_meta( $channel->term_id, Channel::CHANNEL_ID, true );
			do_action( 'bigcommerce/log', Error_Log::DEBUG, __( 'Process single channel', 'bigcommerce' ), [
				'channel_id' => $channel_id,
			] );
			try {
				/** @var \BigCommerce\Api\v3\Model\CurrencyAssignments $assignment */
				$assignment = $this->currencies_v3_api->getChannelCurrencyAssignments( $channel_id )->getData();
				do_action( 'bigcommerce/log', Error_Log::DEBUG, __( 'Currencies assignment', 'bigcommerce' ), [
					'channel_id'       => $channel_id,
					'enabled_currency' => $assignment->getEnabledCurrencies(),
					'default_currency' => $assignment->getDefaultCurrency(),
				] );
				update_term_meta( $channel->term_id, Settings\Sections\Currency::CHANNEL_ALLOWED_CURRENCY, $assignment->getEnabledCurrencies() );
				update_term_meta( $channel->term_id, Settings\Sections\Currency::CHANNEL_CURRENCY_CODE, $assignment->getDefaultCurrency() );
			} catch ( \Throwable $exception ) {
				do_action( 'bigcommerce/log', Error_Log::ERROR, __( 'Could not retrieve channel currencies', 'bigcommerce' ), [
					'channel_id' => $channel_id,
					'message'    => $exception->getMessage(),
					'code'       => $exception->getCode(),
				] );
			}
		}
	}
}
