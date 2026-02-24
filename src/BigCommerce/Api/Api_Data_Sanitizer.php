<?php

namespace BigCommerce\Api;

/**
 * Trait Api_Data_Sanitizer
 *
 * Provides methods to sanitize various types of data.
 *
 * This trait contains helper methods for sanitizing and normalizing different 
 * types of data such as integers, doubles, strings, booleans, and dates. These 
 * methods ensure that the data is properly formatted before being used in the 
 * application.
 *
 * @package BigCommerce\Api
 */
trait Api_Data_Sanitizer {

    /**
     * Sanitizes an integer value.
     *
     * This method converts a scalar value to an integer. If the value is not 
     * scalar (e.g., an array or object), it returns 0.
     *
     * @param mixed $value The value to sanitize.
     * 
     * @return int The sanitized integer value.
     */
    protected function sanitize_int( $value ) {
        if ( is_scalar( $value ) ) {
            return intval( $value );
        }

        return 0;
    }

    /**
     * Sanitizes a double (floating-point) value.
     *
     * This method converts a scalar value to a double. If the value is not 
     * scalar (e.g., an array or object), it returns 0.0.
     *
     * @param mixed $value The value to sanitize.
     * 
     * @return float The sanitized double value.
     */
    protected function sanitize_double( $value ) {
        if ( is_scalar( $value ) ) {
            return doubleval( $value );
        }

        return (double) 0;
    }

    /**
     * Sanitizes a string value.
     *
     * This method converts a scalar value to a string. If the value is not 
     * scalar (e.g., an array or object), it returns an empty string.
     *
     * @param mixed $value The value to sanitize.
     * 
     * @return string The sanitized string value.
     */
    protected function sanitize_string( $value ) {
        if ( is_scalar( $value ) ) {
            return (string) $value;
        }

        return '';
    }

    /**
     * Sanitizes a boolean value.
     *
     * This method converts a scalar value to a boolean. If the value is not 
     * scalar (e.g., an array or object), it returns false.
     *
     * @param mixed $value The value to sanitize.
     * 
     * @return bool The sanitized boolean value.
     */
    protected function sanitize_bool( $value ) {
        if ( is_scalar( $value ) ) {
            return boolval( $value );
        }

        return false;
    }

    /**
     * Sanitizes a date value.
     *
     * This method formats a `DateTime` object to a string in 'Y-m-d H:i:s' format. 
     * If the value is not a `DateTime` object, it returns the current time in 
     * MySQL format.
     *
     * @param mixed $value The value to sanitize.
     * 
     * @return string The sanitized date value in 'Y-m-d H:i:s' format.
     */
    protected function sanitize_date( $value ) {
        if ( $value instanceof \DateTime ) {
            return $value->format( 'Y-m-d H:i:s' );
        }

        return current_time( 'mysql', true );
    }
}
