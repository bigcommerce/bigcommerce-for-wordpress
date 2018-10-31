<?php


namespace BigCommerce\Currency;


use BigCommerce\Settings\Sections\Currency as Position;

class Configurable_Formatter implements Currency_Formatter {
	private $symbol;
	private $position;
	private $decimals;

	public function __construct( $symbol = '¤', $symbol_position = Position::POSITION_LEFT, $decimals = 2 ) {
		$this->symbol   = $symbol;
		$this->position = $symbol_position;
		$this->decimals = (int) $decimals;
	}

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