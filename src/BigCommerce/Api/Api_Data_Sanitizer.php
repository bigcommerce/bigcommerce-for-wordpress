<?php


namespace BigCommerce\Api;


trait Api_Data_Sanitizer {
	protected function sanitize_int( $value ) {
		if ( is_scalar( $value ) ) {
			return intval( $value );
		}

		return 0;
	}

	protected function sanitize_double( $value ) {
		if ( is_scalar( $value ) ) {
			return doubleval( $value );
		}

		return (double) 0;
	}

	protected function sanitize_string( $value ) {
		if ( is_scalar( $value ) ) {
			return (string) $value;
		}

		return '';
	}

	protected function sanitize_bool( $value ) {
		if ( is_scalar( $value ) ) {
			return boolval( $value );
		}

		return false;
	}

	protected function sanitize_date( $value ) {
		if ( $value instanceof \DateTime ) {
			return $value->format( 'Y-m-d H:i:s' );
		}

		return current_time( 'mysql', true );
	}
}
