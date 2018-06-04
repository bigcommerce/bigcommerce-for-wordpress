<?php


namespace BigCommerce\Currency;


class USD_Formatter implements Currency_Formatter {
	public function format( $value ) {
		return sprintf( '$%s', number_format_i18n( $value, 2 ) );
	}
}