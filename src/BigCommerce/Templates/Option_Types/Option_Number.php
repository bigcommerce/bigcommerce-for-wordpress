<?php


namespace BigCommerce\Templates\Option_Types;


class Option_Number extends Option_Type {
	const DEFAULT_VALUE = 'default_value';
	const MIN_VALUE     = 'min_value';
	const MAX_VALUE     = 'max_value';
	const STEP          = 'step';

	protected $template = 'components/option-types/option-number.php';

	public function get_data() {
		$data                        = parent::get_data();
		$data[ self::DEFAULT_VALUE ] = $this->get_default_value( $this->options[ self::CONFIG ] );
		$data[ self::MIN_VALUE ]     = $this->get_min_value( $this->options[ self::CONFIG ] );
		$data[ self::MAX_VALUE ]     = $this->get_max_value( $this->options[ self::CONFIG ] );
		$data[ self::STEP ]          = $this->get_step( $this->options[ self::CONFIG ] );

		if ( $data[ self::MIN_VALUE ] !== null || $data[ self::MAX_VALUE ] !== null ) {
			$data[ self::REQUIRED ] = true; // a value is implicitly required when a max/min is set
		}

		return $data;
	}

	private function get_default_value( $config ) {
		return empty( $config[ 'default_value' ] ) ? '' : $config[ 'default_value' ];
	}

	private function get_min_value( $config ) {
		if (
			empty( $config[ 'number_limited' ] )
			|| empty( $config[ 'number_limit_mode' ] )
			|| empty( $config[ 'number_lowest_value' ] )
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
			|| empty( $config[ 'number_highest_value' ] )
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