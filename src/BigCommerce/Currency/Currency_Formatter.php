<?php


namespace BigCommerce\Currency;

/**
 * Interface for formatting numeric values into currency strings.
 *
 * Provides a contract for implementing currency formatting functionality with a standardized method.
 */
interface Currency_Formatter {
    /**
     * Formats a numeric value into a currency string.
     *
     * @param string|int|float $value The numeric value to be formatted.
     *
     * @return string The formatted currency value.
     */
    public function format( $value );
}