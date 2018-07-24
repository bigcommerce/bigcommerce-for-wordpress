<?php


namespace BigCommerce\Templates;


class Gift_Certificate_Balance_Form extends Form_Controller {

	protected $template = 'components/gift-certificates/balance-form.php';

	protected function parse_options( array $options ) {
		$defaults = [];

		return wp_parse_args( $options, $defaults );
	}

	public function get_data() {
		$data = parent::get_data();

		return $data;
	}

	protected function get_form_defaults() {
		return [
			'code' => '',
		];
	}
}