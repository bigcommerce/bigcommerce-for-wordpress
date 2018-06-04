<?php

namespace BigCommerce\Container;

use BigCommerce\Currency\Configurable_Formatter;
use BigCommerce\Currency\Intl_Formatter;
use BigCommerce\Settings;
use Pimple\Container;

class Currency extends Provider {
	const CURRENCY_CODE = 'currency.code';
	const FORMATTER     = 'currency.formatter';

	public function register( Container $container ) {
		$container[ self::FORMATTER ] = function ( Container $container ) {
			/**
			 * Filter whether to apply auto-formatting to currency using PHP's
			 * \NumberFormatter class from the intl extension.
			 *
			 * @param bool $auto_format Whether to auto-format
			 */
			$auto_format = apply_filters( 'bigcommerce/settings/currency/auto-format', class_exists( '\NumberFormatter' ) );
			if ( $auto_format ) {
				return new Intl_Formatter( $container[ self::CURRENCY_CODE ] );
			}

			$symbol   = get_option( Settings\Currency::CURRENCY_SYMBOL, '$' );
			$position = get_option( Settings\Currency::CURRENCY_SYMBOL_POSITION, Settings\Currency::POSITION_LEFT );
			$decimals = get_option( Settings\Currency::DECIMAL_UNITS, 2 );

			return new Configurable_Formatter( $symbol, $position, $decimals );
		};

		$container[ self::CURRENCY_CODE ] = function ( Container $container ) {
			$currency = get_option( Settings\Currency::CURRENCY_CODE, 'USD' );

			return $currency ?: 'USD';
		};

		add_filter( 'bigcommerce/currency/format', $this->create_callback( 'format_currency', function ( $formatted, $value ) use ( $container ) {
			return $container[ self::FORMATTER ]->format( $value );
		} ), 10, 2 );
	}
}