<?php


namespace BigCommerce\Api;


class Customer_Api extends v2ApiAdapter {
	/**
	 * @param int    $customer_id
	 * @param string $password
	 *
	 * @return bool
	 */
	public function validatePassword( $customer_id, $password ) {
		$path     = sprintf( '/customers/%d/validate', $customer_id );
		$response = $this->createResource( $path, [
			'password' => $password,
		] );

		return ! empty( $response->success );
	}
}