<?php


namespace BigCommerce\Currency;

/**
 * Formatter class for currency values specifically in USD.
 */
class USD_Formatter implements Currency_Formatter {
    /**
     * Formats a numeric value as a USD currency string.
     *
     * @param float|int|string $value The value to format.
     *
     * @return string The formatted currency value in USD.
     */
    public function format( $value ) {
        return sprintf( '$%s', number_format_i18n( $value, 2 ) );
    }
}