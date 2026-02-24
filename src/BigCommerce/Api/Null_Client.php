<?php


namespace BigCommerce\Api;

/**
 * A placeholder API client that disables API calls.
 * This class is typically used when API configuration is missing or incomplete.
 * 
 * Attempts to make API calls using this client will throw an exception.
 *
 * @package BigCommerce\Api
 * @extends Base_Client
 */
class Null_Client extends Base_Client {

	/**
	 * Attempt to make an API call.
	 *
	 * This method always throws an exception because the client is configured to prevent API calls.
	 * It is intended to signal that required API configuration settings are missing.
	 *
	 * @param string $resourcePath Path to the API method endpoint.
	 * @param string $method       HTTP method to use for the request (e.g., GET, POST).
	 * @param array  $queryParams  Parameters to include in the query string of the URL.
	 * @param array  $postData     Parameters to include in the body of the request.
	 * @param array  $headerParams Headers to include in the request.
	 * @param string|null $responseType The expected response type (optional).
	 * @param string|null $endpointPath The original endpoint path before parameter substitution (optional).
	 *
	 * @throws ConfigurationRequiredException Always thrown with a message indicating missing settings.
	 *
	 * @return mixed This method does not return a value; it always throws an exception.
	 */
	public function callApi( $resourcePath, $method, $queryParams, $postData, $headerParams, $responseType = null, $endpointPath = null ) {
		throw new ConfigurationRequiredException( __( 'Unable to connect to BigCommerce API. Missing required settings', 'bigcommerce' ) );
	}
}