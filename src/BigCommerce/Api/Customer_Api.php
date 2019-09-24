<?php


namespace BigCommerce\Api;

/**
 * Class Customer_Api
 *
 * @method mixed updateCustomer( $customer_id, $profile )
 */
class Customer_Api extends v2ApiAdapter {
	/**
	 * @param int    $customer_id
	 * @param string $password
	 *
	 * @return bool
	 * @throws \InvalidArgumentException When the given customer ID does not exist in BigCommerce
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
	 * Find the customer ID associated with the given email address
	 *
	 * @param string $email
	 *
	 * @return int The customer ID, 0 if not found
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
