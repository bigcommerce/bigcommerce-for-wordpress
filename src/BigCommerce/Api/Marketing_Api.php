<?php


namespace BigCommerce\Api;

use BigCommerce\Api\v3\ApiException;

/**
 * Class Marketing_Api
 *
 * @method array getGiftCertificates( $filter = [] )
 */
class Marketing_Api extends v2ApiAdapter {
	/**
	 * @param $code
	 *
	 * @return Resource
	 * @throws ApiException when the Gift Certificate cannot be retrieved
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