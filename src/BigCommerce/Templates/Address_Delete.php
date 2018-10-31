<?php


namespace BigCommerce\Templates;


use BigCommerce\Forms\Delete_Address_Handler;

class Address_Delete extends Controller {

	const ADDRESS_ID = 'address_id';
	const ACTION     = 'action';
	const URL        = 'url';

	protected $template = 'components/accounts/address-delete.php';


	protected function parse_options( array $options ) {
		$defaults = [
			self::ADDRESS_ID => 0,
		];

		return wp_parse_args( $options, $defaults );
	}

	public function get_data() {
		$data = [
			self::ADDRESS_ID => $this->options[ self::ADDRESS_ID ],
			self::URL        => '',
			self::ACTION     => Delete_Address_Handler::ACTION,
		];

		return $data;
	}

}