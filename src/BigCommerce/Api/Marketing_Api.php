<?php


namespace BigCommerce\Api;

use BigCommerce\Api\v3\ApiException;

/**
 * Provides methods for interacting with marketing-related resources in BigCommerce,
 * including gift certificates.
 *
 * @method array getGiftCertificates( array $filter = [] ) Retrieve a list of gift certificates with optional filtering.
 *
 * @package BigCommerce\Api
 * @extends v2ApiAdapter
 */
class Marketing_Api extends v2ApiAdapter {
	
	/**
	 * Retrieve a gift certificate by its code.
	 *
	 * Searches for a gift certificate matching the given code and returns the resource if found.
	 * Throws an exception if no matching gift certificate is found.
	 *
	 * @param string $code The gift certificate code to search for.
	 *
	 * @return Resource The gift certificate resource object.
	 *
	 * @throws ApiException If no gift certificate matches the provided code, or if the API call fails.
	 */
	public function get_gift_certificate_by_code( $code ) {
		$response = $this->getGiftCertificates( [
			'code' => $code,
		] );

		if ( empty( $response ) || ! is_array( $response ) ) {
			$connection = $this->getConnection();
			throw new ApiException( __( 'No gift certificate found matching the given code', 'bigcommerce' ), $connection->getStatus(), $connection->getHeaders(), $connection->getBody() );
		}

		return reset( $response );
	}
}