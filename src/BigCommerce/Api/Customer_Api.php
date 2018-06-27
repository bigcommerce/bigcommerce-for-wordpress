<?php


namespace BigCommerce\Api;


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
}