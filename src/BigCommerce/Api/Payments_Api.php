<?php


namespace BigCommerce\Api;

/**
 * Provides methods for interacting with the Payments API in BigCommerce.
 * Includes functionality to retrieve payment methods and count them with optional filters.
 *
 * @package BigCommerce\Api
 * @extends v2ApiAdapter
 */
class Payments_Api extends v2ApiAdapter {

	/**
	 * Retrieve the count of available payment methods.
	 *
	 * This method counts payment methods returned from the `/payments/methods` endpoint.
	 * It includes a workaround for inconsistent data types in the API response.
	 *
	 * @param bool $include_test_mode Whether to include methods in test mode in the count.
	 *                                Defaults to `false`, which excludes test mode methods.
	 *
	 * @return int|bool The number of payment methods, or `false` if the API response is empty or invalid.
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

	/**
	 * Retrieve all available payment methods.
	 *
	 * This method fetches the collection of payment methods from the `/payments/methods` endpoint.
	 *
	 * @return array An array of payment method resources.
	 */
	public function get_payment_methods() {
		return $this->getCollection( '/payments/methods' );
	}
}
