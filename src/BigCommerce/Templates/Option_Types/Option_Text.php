<?php


namespace BigCommerce\Templates\Option_Types;


class Option_Text extends Option_Type {
	const DEFAULT_VALUE = 'default_value';
	const MINLENGTH     = 'minlength';
	const MAXLENGTH     = 'maxlength';

	protected $template = 'components/option-types/option-text.php';

	public function get_data() {
		$data                        = parent::get_data();
		$data[ self::DEFAULT_VALUE ] = $this->get_default_value( $this->options[ self::CONFIG ] );
		$data[ self::MINLENGTH ]     = $this->get_minlength( $this->options[ self::CONFIG ] );
		$data[ self::MAXLENGTH ]     = $this->get_maxlength( $this->options[ self::CONFIG ] );

		return $data;
	}

	private function get_default_value( $config ) {
		return empty( $config[ 'default_value' ] ) ? '' : $config[ 'default_value' ];
	}

	private function get_minlength( $config ) {
		if ( empty( $config[ 'text_characters_limited' ] ) || empty( $config[ 'text_min_length' ] ) ) {
			return 0;
		}

		return absint( $config[ 'text_min_length' ] );
	}

	private function get_maxlength( $config ) {
		if ( empty( $config[ 'text_characters_limited' ] ) || empty( $config[ 'text_max_length' ] ) ) {
			return 0;
		}

		return absint( $config[ 'text_max_length' ] );
	}


}