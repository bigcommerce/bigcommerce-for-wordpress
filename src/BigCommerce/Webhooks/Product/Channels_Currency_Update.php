<?php

namespace BigCommerce\Webhooks\Product;

use BigCommerce\Api\v3\Api\CurrencyApi;
use BigCommerce\Logging\Error_Log;
use BigCommerce\Settings\Sections\Currency;
use BigCommerce\Taxonomies\Channel\Channel;

class Channels_Currency_Update {

	/**
	 * @var \BigCommerce\Api\v3\Api\CurrencyApi
	 */
	private $api;

	public function __construct( CurrencyApi $api ) {
		$this->api          = $api;
	}

	public function handle_request( int $channel_id ) {
		do_action( 'bigcommerce/log', Error_Log::INFO, __( 'Trigger channel currency update webhook', 'bigcommerce' ), [
			'channel_id' => $channel_id,
		], 'webhooks' );

		$channel = $this->get_channel( $channel_id );

		if ( empty( $channel ) ) {
			do_action( 'bigcommerce/log', Error_Log::INFO, __( 'Requested channel does not exist', 'bigcommerce' ), [
				'channel_id' => $channel_id,
			], 'webhooks' );

			return;
		}

		/**
		 * @var \BigCommerce\Api\v3\Model\CurrencyAssignments
		 */
		$assignment = $this->get_channel_currency_assignment( $channel_id );

		if ( empty( $assignment ) ) {
			do_action( 'bigcommerce/log', Error_Log::INFO, __( 'Unable to retrieve channel currencies', 'bigcommerce' ), [
				'channel_id' => $channel_id,
			], 'webhooks' );

			return;
		}

		update_term_meta( $channel->term_id, Currency::CHANNEL_ALLOWED_CURRENCY, $assignment->getEnabledCurrencies() );
		update_term_meta( $channel->term_id, Currency::CHANNEL_CURRENCY_CODE, $assignment->getDefaultCurrency() );
	}

	/**
	 * @param int $channel_id
	 *
	 * @return \BigCommerce\Api\v3\Model\CurrencyAssignments|false
	 */
	protected function get_channel_currency_assignment( int $channel_id ) {
		try {
			return $this->api->getChannelCurrencyAssignments( $channel_id )->getData();
		} catch ( \Throwable $exception ) {
			do_action( 'bigcommerce/log', Error_Log::ERROR, __( 'Could not retrieve channel currency data', 'bigcommerce' ), [
					'channel_id' => $channel_id,
					'code'       => $exception->getCode(),
					'message'    => $exception->getMessage(),
					'trace'      => $exception->getTraceAsString(),
			], 'webhooks' );

			return false;
		}
	}

	/**
	 * @param int $channel_id
	 *
	 * @return false|int|string|\WP_Term
	 */
	protected function get_channel( int $channel_id ) {
		$channels = get_terms( [
			'taxonomy'   => Channel::NAME,
			'meta_key'   => Channel::CHANNEL_ID,
			'meta_value' => $channel_id,
			'meta_query' => [
				[
					'key'     => Channel::STATUS,
					'value'   => [ Channel::STATUS_PRIMARY, Channel::STATUS_CONNECTED ],
					'compare' => 'IN',
				],
			],
		] );

		if ( empty( $channels ) || is_wp_error( $channels ) ) {
			do_action( 'bigcommerce/log', Error_Log::INFO, __( 'Could not find the channel', 'bigcommerce' ), [
				'channel_id' => $channel_id,
			], 'webhooks' );

			return false;
		}

		return reset( $channels );
	}

}
