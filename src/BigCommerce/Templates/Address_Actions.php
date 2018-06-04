<?php


namespace BigCommerce\Templates;


class Address_Actions extends Controller {

	const FORM        = 'form';
	const DELETE_FORM = 'delete_form';
	const ADDRESS     = 'address';

	protected $template = 'components/address-actions.php';


	protected function parse_options( array $options ) {
		$defaults = [
			self::ADDRESS => [],
		];

		return wp_parse_args( $options, $defaults );
	}

	public function get_data() {
		$data = [
			self::FORM        => $this->get_form(),
			self::DELETE_FORM => $this->get_delete_form(),
		];

		return $data;
	}

	protected function get_form() {
		$form = new Address_Form( $this->options[ self::ADDRESS ] );

		return $form->render();
	}

	protected function get_delete_form() {
		$id = isset( $this->options[ self::ADDRESS ][ 'id' ] ) ? $this->options[ self::ADDRESS ][ 'id' ] : 0;
		if ( empty( $id ) ) {
			return '';
		}
		$form = new Address_Delete( [
			Address_Delete::ADDRESS_ID => $this->options[ self::ADDRESS ][ 'id' ],
		] );

		return $form->render();
	}

}