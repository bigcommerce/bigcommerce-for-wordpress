<?php


namespace BigCommerce\Taxonomies\Channel;

use BigCommerce\Exceptions\Channel_Not_Found_Exception;
use BigCommerce\Settings\Sections\Currency;

class Currency_Filter {
	/**
	 * Filter the currency based on the currently active channel
	 *
	 * @param string $currency_code
	 *
	 * @return string
	 */
	public function filter_currency( $currency_code ) {
		try {
			$connections     = new Connections();
			$current_channel = $connections->current();
		} catch ( Channel_Not_Found_Exception $e ) {
			return $currency_code; // fall back to the default if no channel is configured
		}

		$channel_currency = get_term_meta( $current_channel->term_id, Currency::CHANNEL_CURRENCY_CODE, true );
		if ( empty( $channel_currency ) ) {
			return $currency_code; // fall back to the default if no currency is configured for the channel
		}

		$enabled = get_option( Currency::ENABLED_CURRENCIES );
		if ( ! array_key_exists( $channel_currency, $enabled ) ) {
			return $currency_code; // fall back to the default if the selected currency is not enabled
		}

		return $channel_currency;
	}
}
