<?php


namespace BigCommerce\Templates\Option_Types;

class Option_Swatch extends Option_Type {

	protected $template = 'components/option-types/option-swatch.php';

	protected function get_options() {
		return array_map( function ( $option ) {
			if ( ! empty( $option[ 'value_data' ][ 'image_url' ] ) ) {
				$option[ 'type' ] = 'image';
				$option[ 'src' ]  = $option[ 'value_data' ][ 'image_url' ];
			} elseif ( ! empty( $option[ 'value_data' ][ 'colors' ] ) ) {
				$colors             = $option[ 'value_data' ][ 'colors' ];
				$option[ 'type' ]   = sprintf( '%d-color', count( $colors ) );
				$option[ 'colors' ] = array_map( 'sanitize_hex_color', $colors );
			} else {
				$option[ 'type' ]   = '1-color';
				$option[ 'colors' ] = [ '#999999' ];
			}

			return $option;
		}, parent::get_options() );
	}


}