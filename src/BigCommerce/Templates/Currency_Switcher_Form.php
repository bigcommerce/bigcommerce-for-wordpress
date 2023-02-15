<?php

namespace BigCommerce\Templates;

use BigCommerce\Settings\Sections\Currency as Currency_Settings;

class Currency_Switcher_Form extends Controller {
	const ENABLED_CURRENCIES = 'enabled_currencies';
	const SELECTED_CURRENCY  = 'selected_currency';

	protected $template = 'components/currency-switcher-form.php';
	protected $wrapper_tag = 'div';
	protected $wrapper_classes = [ 'bc-currency-switcher-form-wrapper' ];
	protected $wrapper_attributes = [
		'data-js' => 'bc-currency-switcher-form-wrapper',
	];

	protected function parse_options( array $options ) {
		$defaults = [
			self::ENABLED_CURRENCIES => [],
			self::SELECTED_CURRENCY  => get_option( Currency_Settings::CURRENCY_CODE, 'USD' ),
		];

		return wp_parse_args( $options, $defaults );
	}

	public function get_data() {
		$data = [
			self::ENABLED_CURRENCIES => $this->options[ self::ENABLED_CURRENCIES ],
			self::SELECTED_CURRENCY  => $this->options[ self::SELECTED_CURRENCY ],
		];

		return $data;
	}

}