<?php


namespace BigCommerce\Templates;


class Address_Formatted extends Controller {
	// Keys match those returned from the BigCommerce API

	const ADDRESS_ID = 'id';
	const FIRST_NAME = 'first_name';
	const LAST_NAME  = 'last_name';
	const COMPANY    = 'company';
	const STREET_1   = 'street_1';
	const STREET_2   = 'street_2';
	const CITY       = 'city';
	const STATE      = 'state';
	const ZIP        = 'zip';
	const COUNTRY    = 'country';
	const PHONE      = 'phone';

	protected $template = 'components/accounts/address-formatted.php';


	protected function parse_options( array $options ) {
		$defaults = [
			self::ADDRESS_ID => 0,
			self::FIRST_NAME => '',
			self::LAST_NAME  => '',
			self::COMPANY    => '',
			self::STREET_1   => '',
			self::STREET_2   => '',
			self::CITY       => '',
			self::STATE      => '',
			self::ZIP        => '',
			self::COUNTRY    => '',
			self::PHONE      => '',
		];

		return wp_parse_args( $options, $defaults );
	}

	public function get_data() {
		return $this->options;
	}


}