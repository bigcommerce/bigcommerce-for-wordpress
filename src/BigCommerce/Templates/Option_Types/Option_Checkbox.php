<?php


namespace BigCommerce\Templates\Option_Types;


class Option_Checkbox extends Option_Type {
	const CHECKED        = 'checked';
	const CHECKBOX_LABEL = 'checkbox_label';
	const CHECKBOX_VALUE = 'checkbox_value';

	protected $template = 'components/option-types/option-checkbox.php';

	public function get_data() {
		$data                         = parent::get_data();
		$data[ self::CHECKED ]        = $this->get_default_value( $this->options[ self::CONFIG ] );
		$data[ self::CHECKBOX_LABEL ] = $this->get_checkbox_label( $this->options[ self::CONFIG ] );
		$data[ self::CHECKBOX_VALUE ] = $this->get_checkbox_value( $this->options[ self::OPTIONS ] );

		return $data;
	}

	private function get_default_value( $config ) {
		return ! empty( $config[ 'checked_by_default' ] );
	}

	private function get_checkbox_label( $config ) {
		return empty( $config[ 'checkbox_label' ] ) ? '' : $config[ 'checkbox_label' ];
	}

	private function get_checkbox_value( $options ) {
		foreach ( $options as $option ) {
			if ( ! empty( $option[ 'value_data' ][ 'checked_value' ] ) && ! empty( $option[ 'id' ] ) ) {
				return $option[ 'id' ];
			}
		}

		return 1; // we should never reach this line if the data is valid
	}

}