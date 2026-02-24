<?php


namespace BigCommerce\Currency;

use BigCommerce\Settings;

/**
 * Factory class to create and manage currency formatters.
 */
class Formatter_Factory {
    /**
     * Cache of created currency formatters.
     *
     * @var array
     */
    private $formatters = [];

    /**
     * Retrieves a currency formatter for the given currency code.
     *
     * Creates a new formatter if one does not already exist for the currency code.
     *
     * @param string $currency_code The currency code for which to retrieve the formatter.
     *
     * @return Intl_Formatter|Configurable_Formatter The currency formatter.
     */
    public function get( $currency_code ) {
        if ( array_key_exists( $currency_code, $this->formatters ) ) {
            return $this->formatters[ $currency_code ];
        }

        if ( $this->auto_format() ) {
            $this->formatters[ $currency_code ] = $this->make_auto_formatter( $currency_code );
            return $this->formatters[ $currency_code ];
        }

        $enabled = $this->enabled_currencies();
        if ( isset( $enabled[ $currency_code ] ) ) {
            $this->formatters[ $currency_code ] = $this->make_configurable_formatter_from_currency( $enabled[ $currency_code ] );
        } else {
            $this->formatters[ $currency_code ] = $this->make_configurable_formatter_from_options();
        }

        return $this->formatters[ $currency_code ];
    }

    private function auto_format() {
        /**
         * Filter whether to apply auto-formatting to currencies.
         *
         * @param bool $auto_format Whether to enable auto-formatting.
         */
        return apply_filters( 'bigcommerce/settings/currency/auto-format', class_exists( '\NumberFormatter' ) );
    }

    private function enabled_currencies() {
        return get_option( Settings\Sections\Currency::ENABLED_CURRENCIES, [] );
    }

    private function make_auto_formatter( $currency_code ) {
        return new Intl_Formatter( $currency_code );
    }

    private function make_configurable_formatter_from_currency( $currency ) {
        $symbol   = $currency['token'];
        $position = $currency['token_location'];
        $decimals = $currency['decimal_places'];

        return new Configurable_Formatter( $symbol, $position, $decimals );
    }

    private function make_configurable_formatter_from_options() {
        $symbol   = get_option( Settings\Sections\Currency::CURRENCY_SYMBOL, '$' );
        $position = get_option( Settings\Sections\Currency::CURRENCY_SYMBOL_POSITION, Settings\Sections\Currency::POSITION_LEFT );
        $decimals = get_option( Settings\Sections\Currency::DECIMAL_UNITS, 2 );

        return new Configurable_Formatter( $symbol, $position, $decimals );
    }
}
