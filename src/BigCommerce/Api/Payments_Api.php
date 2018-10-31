<?php


namespace BigCommerce\Api;


class Payments_Api extends v2ApiAdapter {

	/**
	 * A temporary workaround because the API does not return a consistent
	 * data type for the /payments/methods endpoint
	 *
	 * @return bool
	 */
	public function get_payment_methods_count() {
		$connection = $this->getConnection();
		$response   = $connection->get( Client::$api_path . '/payments/methods' );
		if ( empty( $response ) ) {
			return false;
		}
		if ( ! is_array( $response ) ) {
			return 1;
		}

		return count( $response );
	}

	public function get_payment_methods() {
		return $this->getCollection( '/payments/methods' );
	}
}