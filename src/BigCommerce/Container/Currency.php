<?php

namespace BigCommerce\Container;

use BigCommerce\Currency\Configurable_Formatter;
use BigCommerce\Currency\Intl_Formatter;
use BigCommerce\Settings;
use Pimple\Container;

class Currency extends Provider {
	const FORMATTER     = 'currency.formatter';

	public function register( Container $container ) {
		$container[ self::FORMATTER ] = $container->factory( function ( Container $container ) {
			static $formatters = [];
			$currency = get_option( Settings\Sections\Currency::CURRENCY_CODE, 'USD' );
			if ( array_key_exists( $currency, $formatters ) ) {
				return $formatters[ $currency ];
			}
			/**
			 * Filter whether to apply auto-formatting to currency using PHP's
			 * \NumberFormatter class from the intl extension.
			 *
			 * @param bool $auto_format Whether to auto-format
			 */
			$auto_format = apply_filters( 'bigcommerce/settings/currency/auto-format', class_exists( '\NumberFormatter' ) );
			if ( $auto_format ) {
				$formatters[ $currency ] = new Intl_Formatter( get_option( Settings\Sections\Currency::CURRENCY_CODE, 'USD' ) );
				return $formatters[ $currency ];
			}

			$symbol   = get_option( Settings\Sections\Currency::CURRENCY_SYMBOL, '$' );
			$position = get_option( Settings\Sections\Currency::CURRENCY_SYMBOL_POSITION, Settings\Sections\Currency::POSITION_LEFT );
			$decimals = get_option( Settings\Sections\Currency::DECIMAL_UNITS, 2 );

			$formatters[ $currency ] = new Configurable_Formatter( $symbol, $position, $decimals );
			return $formatters[ $currency ];
		} );


		add_filter( 'bigcommerce/currency/format', $this->create_callback( 'format_currency', function ( $formatted, $value ) use ( $container ) {
			return $container[ self::FORMATTER ]->format( $value );
		} ), 10, 2 );
	}
}