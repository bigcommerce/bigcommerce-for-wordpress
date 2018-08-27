<?php


namespace BigCommerce\Api;

class Null_Client extends Base_Client {

	/**
	 * (Doesn't) make the HTTP call (Sync)
	 *
	 * @param string $resourcePath path to method endpoint
	 * @param string $method       method to call
	 * @param array  $queryParams  parameters to be place in query URL
	 * @param array  $postData     parameters to be placed in POST body
	 * @param array  $headerParams parameters to be place in request header
	 * @param string $responseType expected response type of the endpoint
	 * @param string $endpointPath path to method endpoint before expanding parameters
	 *
	 * @throws \BigCommerce\Api\v3\ApiException on a non 2xx response
	 * @return mixed
	 */
	public function callApi( $resourcePath, $method, $queryParams, $postData, $headerParams, $responseType = null, $endpointPath = null ) {
		throw new ConfigurationRequiredException( __( 'Unable to connect to BigCommerce API. Missing required settings', 'bigcommerce' ) );
	}
}