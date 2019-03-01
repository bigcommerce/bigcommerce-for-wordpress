<?php


namespace BigCommerce\Templates\Option_Types;


class Option_Date extends Option_Type {
	const DEFAULT_VALUE = 'default_value';
	const MIN_VALUE     = 'min_value';
	const MAX_VALUE     = 'max_value';

	protected $template        = 'components/option-types/option-date.php';
	protected $wrapper_t       = 'div';
	protected $wrapper_classes = [ 'bc-product-form__control', 'bc-product-form__control--date' ];

	public function get_data() {
		$data                        = parent::get_data();
		$data[ self::DEFAULT_VALUE ] = $this->get_default_value( $this->options[ self::CONFIG ] );
		$data[ self::MIN_VALUE ]     = $this->get_min_value( $this->options[ self::CONFIG ] );
		$data[ self::MAX_VALUE ]     = $this->get_max_value( $this->options[ self::CONFIG ] );

		return $data;
	}

	private function get_default_value( $config ) {
		return empty( $config[ 'default_value' ] ) ? '' : $this->format_date( $config[ 'default_value' ] );
	}

	private function get_min_value( $config ) {
		if (
			empty( $config[ 'date_limited' ] )
			|| empty( $config[ 'date_limit_mode' ] )
			|| empty( $config[ 'date_earliest_value' ] )
			|| ! in_array( $config[ 'date_limit_mode' ], [ 'earliest', 'range' ] )
		) {
			return '';
		}

		return $this->format_date( $config[ 'date_earliest_value' ] );
	}

	private function get_max_value( $config ) {
		if (
			empty( $config[ 'date_limited' ] )
			|| empty( $config[ 'date_limit_mode' ] )
			|| empty( $config[ 'date_latest_value' ] )
			|| ! in_array( $config[ 'date_limit_mode' ], [ 'latest', 'range', ] )
		) {
			return '';
		}

		return $this->format_date( $config[ 'date_latest_value' ] );
	}

	private function format_date( $date_string ) {
		try {
			$date = new \DateTimeImmutable( $date_string );

			return $date->format( 'Y-m-d' );
		} catch ( \Exception $e ) {
			return '';
		}
	}


}