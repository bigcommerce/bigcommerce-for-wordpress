<?php


namespace BigCommerce\Templates;


class Address_New extends Controller {

	const FORM = 'form';

	protected $template = 'components/address-new.php';


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
		$form = new Address_Form( [] );
		return $form->render();
	}


}