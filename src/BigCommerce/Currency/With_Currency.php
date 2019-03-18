<?php


namespace BigCommerce\Currency;


trait With_Currency {

	/**
	 * @param float  $value       The currency value to format
	 * @param string $empty_value What to return if the value is empty. Pass `null` to format anyway.
	 *
	 * @return string
	 */
	protected function format_currency( $value, $empty_value = '' ) {
		if ( ! (float) $value && isset( $empty_value ) ) {
			return $empty_value;
		}

		/**
		 * Format a price for the current currency and locale
		 *
		 * @param string $formatted The formatted currency string
		 * @param float  $value     The price to format
		 */
		return apply_filters( 'bigcommerce/currency/format', sprintf( '¤%0.2f', $value ), $value );
	}
}