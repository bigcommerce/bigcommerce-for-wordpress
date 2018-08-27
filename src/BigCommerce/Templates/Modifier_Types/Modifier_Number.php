<?php


namespace BigCommerce\Templates\Modifier_Types;


class Modifier_Number extends Modifier_Type {
	const DEFAULT_VALUE = 'default_value';
	const MIN_VALUE     = 'min_value';
	const MAX_VALUE     = 'max_value';
	const STEP          = 'step';

	protected $template = 'components/modifier-types/modifier-number.php';

	public function get_data() {
		$data                        = parent::get_data();
		$data[ self::DEFAULT_VALUE ] = $this->get_default_value( $this->options[ self::CONFIG ] );
		$data[ self::MIN_VALUE ]     = $this->get_min_value( $this->options[ self::CONFIG ] );
		$data[ self::MAX_VALUE ]     = $this->get_max_value( $this->options[ self::CONFIG ] );
		$data[ self::STEP ]          = $this->get_step( $this->options[ self::CONFIG ] );

		return $data;
	}

	private function get_default_value( $config ) {
		return empty( $config[ 'default_value' ] ) ? '' : $config[ 'default_value' ];
	}

	private function get_min_value( $config ) {
		if (
			empty( $config[ 'number_limited' ] )
			|| empty( $config[ 'number_limit_mode' ] )
			|| ! in_array( $config[ 'number_limit_mode' ], [ 'lowest', 'range' ] )
		) {
			return null;
		}

		return intval( $config[ 'number_lowest_value' ] );
	}

	private function get_max_value( $config ) {
		if (
			empty( $config[ 'number_limited' ] )
			|| empty( $config[ 'number_limit_mode' ] )
			|| ! in_array( $config[ 'number_limit_mode' ], [ 'highest', 'range', ] )
		) {
			return null;
		}

		return intval( $config[ 'number_highest_value' ] );
	}

	private function get_step( $config ) {
		if ( empty( $config[ 'number_integers_only' ] ) ) {
			return '0.001';
		} else {
			return '1';
		}
	}


}