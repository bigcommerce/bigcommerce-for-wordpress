<?php


namespace BigCommerce\Currency;

/**
 * Provides currency formatting functionality for classes.
 */
trait With_Currency {

    /**
     * Formats a numeric value as a currency string.
     *
     * @param float  $value       The currency value to format.
     * @param string $empty_value The value to return if $value is empty. Pass `null` to format anyway.
     *
     * @return string The formatted currency string or the empty value.
     */
    protected function format_currency( $value, $empty_value = '' ) {
        if ( ! (float) $value && isset( $empty_value ) ) {
            return $empty_value;
        }

        /**
         * Filters the formatted currency string for the current currency and locale.
         *
         * @param string $formatted The formatted currency string.
         * @param float  $value     The currency value being formatted.
         */
        return apply_filters( 'bigcommerce/currency/format', sprintf( '¤%0.2f', $value ), $value );
    }
}