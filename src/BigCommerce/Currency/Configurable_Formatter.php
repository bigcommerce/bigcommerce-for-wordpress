<?php


namespace BigCommerce\Currency;


use BigCommerce\Settings\Sections\Currency as Position;

/**
 * Class for configuring and formatting currency values.
 *
 * Allows for customizable currency symbol, position, and decimal precision when formatting monetary values.
 */
class Configurable_Formatter implements Currency_Formatter {
    /**
     * The currency symbol.
     *
     * @var string
     */
    private $symbol;

    /**
     * The position of the currency symbol relative to the value.
     *
     * @var string
     */
    private $position;

    /**
     * The number of decimal places for formatting.
     *
     * @var int
     */
    private $decimals;

    /**
     * Constructor for Configurable_Formatter.
     *
     * @param string $symbol           The currency symbol to use (default is '¤').
     * @param string $symbol_position  The position of the currency symbol, using constants from `Position` (default is left).
     * @param int    $decimals         The number of decimal places for formatted values (default is 2).
     */
    public function __construct( $symbol = '¤', $symbol_position = Position::POSITION_LEFT, $decimals = 2 ) {
        $this->symbol   = $symbol;
        $this->position = $symbol_position;
        $this->decimals = (int) $decimals;
    }

    /**
     * Formats a currency value based on the configured settings.
     *
     * @param float $value The numeric value to format.
     *
     * @return string The formatted currency value with the symbol in the configured position.
     */
    public function format( $value ) {
        $formatted = number_format_i18n( $value, $this->decimals );
        switch ( $this->position ) {
            case Position::POSITION_LEFT_SPACE:
                return $this->symbol . ' ' . $formatted;
            case Position::POSITION_RIGHT:
                return $formatted . $this->symbol;
            case Position::POSITION_RIGHT_SPACE:
                return $formatted . ' ' . $this->symbol;
            case Position::POSITION_LEFT:
            default:
                return $this->symbol . $formatted;
        }
    }

	
}