<?php

namespace BigCommerce\Container;

use BigCommerce\Currency\Formatter_Factory;
use BigCommerce\Settings;
use Pimple\Container;

class Currency extends Provider {
	const FORMATTER = 'currency.formatter';
	const FACTORY   = 'currency.formatter.factory';

	public function register( Container $container ) {
		$container[ self::FACTORY ] = function ( Container $container ) {
			return new Formatter_Factory();
		};

		$container[ self::FORMATTER ] = $container->factory( function ( Container $container ) {
			$currency = get_option( Settings\Sections\Currency::CURRENCY_CODE, 'USD' );

			return $container[ self::FACTORY ]->get( $currency );
		} );


		add_filter( 'bigcommerce/currency/format', $this->create_callback( 'format_currency', function ( $formatted, $value ) use ( $container ) {
			return $container[ self::FORMATTER ]->format( $value );
		} ), 10, 2 );
	}
}
