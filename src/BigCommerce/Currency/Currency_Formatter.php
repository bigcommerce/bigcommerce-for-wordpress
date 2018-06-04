<?php


namespace BigCommerce\Currency;


interface Currency_Formatter {
	/**
	 * Format a number into a currency string
	 *
	 * @param string|int|float $value
	 *
	 * @return string The formatted currency value
	 */
	public function format( $value );
}