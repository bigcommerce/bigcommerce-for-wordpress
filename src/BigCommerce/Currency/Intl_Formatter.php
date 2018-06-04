<?php


namespace BigCommerce\Currency;


class Intl_Formatter implements Currency_Formatter {
	private $formatter;
	private $currency;

	public function __construct( $currency ) {
		$this->formatter = new \NumberFormatter( $this->get_locale(), \NumberFormatter::CURRENCY );
		$this->currency  = $currency;
	}

	private function get_locale() {
		return \get_locale();
	}

	public function format( $value ) {
		return $this->formatter->formatCurrency( $value, $this->currency );
	}


}