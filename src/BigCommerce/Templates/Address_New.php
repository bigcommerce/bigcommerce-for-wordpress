<?php


namespace BigCommerce\Templates;


class Address_New extends Controller {

	const FORM = 'form';

	protected $template = 'components/accounts/address-new.php';
	protected $wrapper_tag = 'div';
	protected $wrapper_classes = [ 'bc-account-address__actions', 'bc-account-address__actions--new' ];
	protected $wrapper_attributes = [ 'data-js' => 'bc-account-address-actions' ];


	protected function parse_options( array $options ) {
		$defaults = [];

		return wp_parse_args( $options, $defaults );
	}

	public function get_data() {
		$data                    = $this->options;
		$data[ self::FORM ] = $this->get_form();
		return $data;
	}

	protected function get_form() {
		$form = Address_Form::factory( [] );
		return $form->render();
	}


}