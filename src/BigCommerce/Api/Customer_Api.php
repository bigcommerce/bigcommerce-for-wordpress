<?php


namespace BigCommerce\Api;

/**
 * Provides methods for interacting with BigCommerce customers via the v2 API.
 * Includes functionality for validating customer passwords and retrieving customer
 * information by email address.
 *
 * @method mixed updateCustomer( int $customer_id, array $profile ) Updates a customer's profile.
 * @package BigCommerce\Api
 * @extends v2ApiAdapter
 */
class Customer_Api extends v2ApiAdapter {

	/**
	 * Validate a customer's password.
	 *
	 * Checks if the provided password matches the stored password for the given customer ID.
	 * Throws an exception if the customer ID does not exist.
	 *
	 * @param int    $customer_id The ID of the customer to validate.
	 * @param string $password    The password to validate.
	 *
	 * @return bool True if the password is valid, false otherwise.
	 *
	 * @throws \InvalidArgumentException If the customer ID is not found.
	 */
	public function validatePassword( $customer_id, $password ) {
		$path     = sprintf( '/customers/%d/validate', $customer_id );
		$response = $this->createResource( $path, [
			'password' => $password,
		] );

		if ( $response === false ) {
			$status_code = $this->getConnection()->getStatus();
			if ( $status_code >= 400 && $status_code <= 499 ) {
				throw new \InvalidArgumentException( __( 'Customer ID not found', 'bigcommerce' ) );
			}

			return false; // all other errors may be temporary server issues
		}

		return ! empty( $response->success );
	}

	/**
	 * Find the customer ID associated with a given email address.
	 *
	 * Searches for a customer by email and returns their ID. Returns 0 if no customer
	 * is found or if an error occurs during the API request.
	 *
	 * @param string $email The email address to search for.
	 *
	 * @return int The customer ID if found, or 0 if not found or on failure.
	 */
	public function find_customer_id_by_email( $email ) {
		try {
			$matches = $this->getCustomers( [
				'email' => $email,
			] );

			if ( empty( $matches ) ) {
				return 0;
			}

			return reset( $matches )->id;
		} catch ( \Exception $e ) {
			return 0;
		}
	}
}
