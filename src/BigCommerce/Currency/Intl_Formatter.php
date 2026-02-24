<?php


namespace BigCommerce\Currency;

/**
 * Formatter class for currency values using PHP's intl extension.
 */
class Intl_Formatter implements Currency_Formatter {
    /**
     * The NumberFormatter instance for formatting currency values.
     *
     * @var \NumberFormatter
     */
    private $formatter;

    /**
     * The currency code used for formatting.
     *
     * @var string
     */
    private $currency;

    /**
     * Initializes the Intl_Formatter with a specific currency code.
     *
     * @param string $currency The currency code (e.g., USD, EUR).
     */
    public function __construct( $currency ) {
        $this->formatter = new \NumberFormatter( $this->get_locale(), \NumberFormatter::CURRENCY );
        $this->currency  = $currency;
    }

    private function get_locale() {
        return \get_locale();
    }

    /**
     * Formats a numeric value as a currency string.
     *
     * @param float|int|string $value The value to format.
     *
     * @return string The formatted currency value.
     */
    public function format( $value ) {
        return $this->formatter->formatCurrency( $value, $this->currency );
    }

	
}