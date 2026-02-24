<?php

namespace BigCommerce\Api;

/**
 * Provides an adapter for interacting with the BigCommerce API. This class includes methods for
 * retrieving collections and resources, as well as creating, updating, and deleting resources.
 *
 * @method array getCollection( $path, $resource = 'Resource' ) Retrieves a collection of resources from the specified path.
 * @method Resource getResource( $path, $resource = 'Resource' ) Retrieves a single resource from the specified path.
 * @method mixed createResource( $path, $object ) Creates a new resource at the specified path.
 * @method mixed updateResource( $path, $object ) Updates an existing resource at the specified path.
 * @method mixed deleteResource( $path ) Deletes a resource at the specified path.
 * @method Connection getConnection() Retrieves the connection instance used to make API requests.
 */
class v2ApiAdapter {
    protected $apiClient;
    protected $client_class = '\Bigcommerce\Api\Client';

    /**
     * Constructor
     *
     * Initializes the API client instance to be used in making requests.
     *
     * @param Base_Client $apiClient The API client to use.
     */
	public function __construct( Base_Client $apiClient ) {
		$this->apiClient = $apiClient;
	}

    /**
     * Magic method to call methods on the client class.
     *
     * Dynamically calls methods on the client class if available. Throws an exception if the method does not exist.
     *
     * @param string $method The method name to call.
     * @param array  $args   The arguments to pass to the method.
     *
     * @return mixed The result of the method call.
     *
     * @throws \BadMethodCallException If the method is not found.
     */
	public function __call( $method, $args ) {
		if ( is_callable( [ $this->client_class, $method ] ) ) {
			return call_user_func_array( [ $this->client_class, $method ], $args );
		}
		throw new \BadMethodCallException( sprintf( 'Unknown method: %s', $method ) );
	}

    /**
     * Retrieve the store hash from the API client configuration.
     *
     * Extracts the store hash from the API client's configuration using the host URL.
     *
     * @return string The store hash.
     */
	protected function get_store_hash() {
		$config = $this->apiClient->getConfig();
		$host   = $config->getHost();
		preg_match( '#stores/([^\/]+)/#', $host, $matches );
		if ( empty( $matches[ 1 ] ) ) {
			return '';
		} else {
			return $matches[ 1 ];
		}
	}

}