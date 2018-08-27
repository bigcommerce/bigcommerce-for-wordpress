<?php


namespace BigCommerce\Api;


/**
 * Class Caching_Client
 *
 * Wraps a short-term cache around API requests.
 * This avoids making the same request (especially
 * for the cart) over and over.
 *
 * The cache is flushed every time a write operation
 * occurs.
 */
class Caching_Client extends Base_Client {
	/** @var string */
	private $cache_group = 'bigcommerce_api';
	/** @var int */
	private $cache_ttl = 60;
	/** @var string */
	private $generation_key = '';

	/**
	 * Make the HTTP call (Sync)
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
	 * @return array
	 */
	public function callApi( $resourcePath, $method, $queryParams, $postData, $headerParams, $responseType = null, $endpointPath = null ) {
		if ( ! in_array( $method, $this->cacheable_methods() ) ) {
			// any write operation increments the cache key
			$this->update_generation_key();

			return parent::callApi( $resourcePath, $method, $queryParams, $postData, $headerParams, $responseType, $endpointPath );
		}

		try {
			$cache_key = $this->build_cache_key( $resourcePath, $method, $queryParams, $postData, $headerParams, $responseType );
			$cached    = wp_cache_get( $cache_key, $this->cache_group );
			if ( ! empty( $cached ) && is_array( $cached ) ) {
				return $cached;
			}
		} catch ( \Exception $e ) {
			return parent::callApi( $resourcePath, $method, $queryParams, $postData, $headerParams, $responseType, $endpointPath );
		}

		$result = parent::callApi( $resourcePath, $method, $queryParams, $postData, $headerParams, $responseType, $endpointPath );

		wp_cache_set( $cache_key, $result, $this->cache_group, $this->cache_ttl );

		return $result;
	}


	/**
	 * @return array A list of all read operations
	 */
	private function cacheable_methods() {
		return [
			self::$GET,
			self::$OPTIONS,
			self::$HEAD,
		];
	}

	/**
	 * Build a unique identifier for the request
	 *
	 * @param $resourcePath
	 * @param $method
	 * @param $queryParams
	 * @param $postData
	 * @param $headerParams
	 * @param $responseType
	 *
	 * @return string
	 */
	private function build_cache_key( $resourcePath, $method, $queryParams, $postData, $headerParams, $responseType ) {
		$args       = [
			'method'       => $method,
			'queryParams'  => $queryParams,
			'postData'     => $postData,
			'headerParams' => $headerParams,
			'responseType' => $responseType,
		];
		$serialized = md5( json_encode( $args ) );

		return $resourcePath . ':' . $serialized . ':' . $this->get_generation_key();
	}

	/**
	 * @return string The generation key for cache versioning
	 */
	private function get_generation_key() {
		if ( empty( $this->generation_key ) ) {
			$this->generation_key = wp_cache_get( 'generation_key', $this->cache_group );
			if ( empty( $this->generation_key ) ) {
				$this->update_generation_key();
			}
		}

		return $this->generation_key;
	}

	/**
	 * @return void Update the generation key based on the current timestamp
	 */
	private function update_generation_key() {
		$this->generation_key = md5( microtime( true ) );
		wp_cache_set( 'generation_key', $this->generation_key, $this->cache_group );
	}
}