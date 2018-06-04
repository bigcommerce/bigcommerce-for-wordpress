<?php


namespace BigCommerce\Templates;

class Option_Swatch extends Option_Type {

	protected $template = 'components/option-swatch.php';

	protected function get_options() {
		return array_map( function ( $option ) {
			if ( ! empty( $option[ 'value_data' ][ 'colors' ] ) ) {
				$option[ 'color' ] = sanitize_hex_color( reset( $option[ 'value_data' ][ 'colors' ] ) );
			} else {
				$option[ 'color' ] = '#999999';
			}

			return $option;
		}, parent::get_options() );
	}


}