<?php


namespace BigCommerce\Api;


class v2ApiAdapter {
	protected $apiClient;
	protected $client_class = '\Bigcommerce\Api\Client';

	/**
	 * Constructor
	 *
	 * @param Base_Client $apiClient The api client to use
	 */
	public function __construct( Base_Client $apiClient ) {
		$this->apiClient = $apiClient;
	}

	public function __call( $method, $args ) {
		if ( is_callable( [ $this->client_class, $method ] ) ) {
			return call_user_func_array( [ $this->client_class, $method ], $args );
		}
		throw new \BadMethodCallException( sprintf( 'Unknown method: %s', $method ) );
	}

	protected function get_store_hash() {
		$config    = $this->apiClient->getConfig();
		$host      = $config->getHost();
		preg_match( '#stores/([^\/]+)/#', $host, $matches );
		if ( empty( $matches[ 1 ] ) ) {
			return '';
		} else {
			return $matches[ 1 ];
		}
	}

}