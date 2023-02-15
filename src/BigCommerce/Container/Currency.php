<?php

namespace BigCommerce\Container;

use BigCommerce\Currency\Currency as Currency_Manager;
use BigCommerce\Currency\Formatter_Factory;
use Pimple\Container;

class Currency extends Provider {
	const CURRENCY      = 'currency';
	const FORMATTER     = 'currency.formatter';
	const FACTORY       = 'currency.formatter.factory';
	const CURRENCY_CODE = 'currency.code';

	public function register( Container $container ) {
		$container[ self::CURRENCY ] = function ( Container $container ) {
			return new Currency_Manager();
		};

		$container[ self::CURRENCY_CODE ] = function ( Container $container ) {
			return $container[ self::CURRENCY ]->get_currency_code();
		};

		$container[ self::FACTORY ] = function ( Container $container ) {
			return new Formatter_Factory();
		};

		$container[ self::FORMATTER ] = $container->factory( function ( Container $container ) {
			return $container[ self::FACTORY ]->get( $container[ self::CURRENCY_CODE ] );
		} );

		add_filter( 'bigcommerce/currency/format', $this->create_callback( 'format_currency', function ( $formatted, $value ) use ( $container ) {
			return $container[ self::FORMATTER ]->format( $value );
		} ), 10, 2 );

		add_filter( 'bigcommerce/currency/code', $this->create_callback( 'filter_currency_code', function () use ( $container ) {
			return $container[ self::CURRENCY_CODE ];
		} ) );

		add_filter( 'bigcommerce/currency/enabled', $this->create_callback( 'filter_enabled_currencies', function () use ( $container ) {
			return $container[ self::CURRENCY ]->get_channel_aware_currencies();
		} ) );
	}
}
