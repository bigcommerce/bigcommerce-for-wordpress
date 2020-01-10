<?php


namespace BigCommerce\Api;


class Payments_Api extends v2ApiAdapter {

	/**
	 * A temporary workaround because the API does not return a consistent
	 * data type for the /payments/methods endpoint
	 *
	 * @param bool $include_test_mode
	 *
	 * @return bool
	 */
	public function get_payment_methods_count( $include_test_mode = false ) {
		$connection = $this->getConnection();
		$response   = $connection->get( Client::$api_path . '/payments/methods' );
		if ( empty( $response ) ) {
			return false;
		}

		if ( ! is_array( $response ) ) {
			$response = [ $response ];
		}

		if ( ! $include_test_mode ) {
			$response = array_filter( $response, function ( $method ) {
				return empty( $method->test_mode );
			} );
		}

		return count( $response );
	}

	public function get_payment_methods() {
		return $this->getCollection( '/payments/methods' );
	}
}
