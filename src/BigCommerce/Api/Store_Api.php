<?php


namespace BigCommerce\Api;


use Firebase\JWT\JWT;

/**
 * Handle retrieving information about the store from APIv2.
 *
 * @package BigCommerce\Api
 */
class Store_Api extends v2ApiAdapter {

    /**
     * Get customer login token by ID.
     *
     * This method generates a JWT token to allow a customer to log in. The token can
     * be used to authenticate the customer in future API requests.
     *
     * @param int    $id           The ID of the customer.
     * @param string $redirectUrl  Optional URL to redirect the customer after login.
     * @param string $requestIp    Optional IP address of the requestor.
     * @param int    $channel_id   Optional channel ID for the customer login.
     *
     * @return string The generated JWT login token.
     * @throws \Exception If the client secret is missing or other errors occur.
     */
	public function getCustomerLoginToken( $id, $redirectUrl = '', $requestIp = '', $channel_id = 0 ) {
		$config     = $this->apiClient->getConfig();
		$client_id  = $config->getClientId();
		$secret     = $config->getClientSecret();
		$store_hash = $this->get_store_hash();
		if ( empty( $secret ) ) {
			throw new \Exception( 'Cannot sign customer login tokens without a client secret' );
		}

		$payload = [
			'iss'         => $client_id,
			'iat'         => $this->get_server_time(),
			'jti'         => bin2hex( random_bytes( 32 ) ),
			'operation'   => 'customer_login',
			'store_hash'  => $store_hash,
			'customer_id' => $id,
		];

		if ( ! empty( $redirectUrl ) ) {
			$payload[ 'redirect_to' ] = $redirectUrl;
		}

		if ( ! empty( $requestIp ) ) {
			$payload[ 'request_ip' ] = $requestIp;
		}

		if ( ! empty( $channel_id ) ) {
			$payload[ 'channel_id' ] = (int) $channel_id;
		}

		return JWT::encode( $payload, $secret, 'HS256' );
	}

    /**
     * Return server time in unix timestamp
     *
     * @return int|mixed
     */
	private function get_server_time() {
		$offset = get_transient( 'bigcommerce_time_offset' );
		if ( $offset === false ) {
			$offset = $this->update_server_time();
		}

		return time() + $offset;
	}

    /**
     * Update server time from /time endpoint. If it is not possible set current unix timestamp.
     * Determine time offset between BC api result and current server time
     *
     * @return int
     */
	private function update_server_time() {
		try {
			$api_time = $this->getResource( '/time' )->time;
		} catch ( \Exception $e ) {
			$api_time = time();
		}
		$now    = time();
		$offset = $api_time - $now;
		set_transient( 'bigcommerce_time_offset', $offset, HOUR_IN_SECONDS );

		return $offset;
	}

    /**
     * Return the list of store analytics settings.
     *
     * This method retrieves the store's analytics settings, such as tracking options.
     *
     * @return array The store's analytics settings.
     */
	public function get_analytics_settings() {
		try {
			$settings = $this->getCollection( '/settings/analytics' );

			if ( empty( $settings ) ) {
				return [];
			}
			$settings = array_map( function ( Resource $resource ) {
				return get_object_vars( $resource->getUpdateFields() );
			}, $settings );
		} catch ( \Throwable $e ) {
			$settings = [];
		}

		return $settings ?: [];
	}

    /**
     * Update store analytics setting by ID.
     *
     * This method updates the analytics settings for a given store using the provided
     * settings array.
     *
     * @param int   $id       The ID of the analytics setting to update.
     * @param array $settings The new settings for the store.
     *
     * @return bool True if the update was successful, false otherwise.
     */
	public function update_analytics_settings( $id, array $settings ) {
		try {
			unset( $settings[ 'id' ] );
			unset( $settings[ 'name' ] );
			// not going to listen for success
			$this->updateResource( sprintf( '/settings/analytics/%d', $id ), $settings );

			return true;
		} catch ( \Exception $e ) {
			// TODO: provide more detailed information about an error
			return false;
		}
	}

    /**
     * Check if site-wide HTTPS option is enabled in BigCommerce.
     *
     * This method checks if the site-wide HTTPS option is enabled for the store.
     *
     * @return bool True if HTTPS is enabled, false otherwise.
     */
	public function get_sitewidehttps_enabled() {
		$resource = $this->get_store_resource();

		/**
		 * get_store_resource() may return false value.
		 * This will cause an issue when we try to access property on non-object
		 */
		$resource = $this->store_resource_exists( $resource, 'features' );

		return ! empty( $resource ) ? $resource->sitewidehttps_enabled : false;
	}

    /**
     * Get store domain.
     *
     * This method retrieves the domain name associated with the store.
     *
     * @return bool The domain name of the store if available, otherwise false.
     */
	public function get_domain() {
		$resource = $this->get_store_resource();

		/**
		 * get_store_resource() may return false value.
		 * This will cause an issue when we try to access property on non-object
		 */
		return $this->store_resource_exists( $resource, 'domain' );
	}

    /**
     * Check whether the provided resource exists.
     *
     * This method checks if a specific property exists in a given resource.
     *
     * @param mixed  $resource The resource object to check.
     * @param string $property The property name to check for.
     *
     * @return bool True if the property exists in the resource, false otherwise.
     */
	public function store_resource_exists( $resource, $property) {
		if ( empty( $resource ) ) {
			return false;
		}

		return $resource->{$property};
	}

    /**
     * Get store resource.
     *
     * This method retrieves the store resource containing details about the store.
     *
     * @return false|Resource The store resource, or false if an error occurs.
     */
	public function get_store_resource() {
		try {
			return $this->getResource( '/store' );
		} catch ( \Exception $e ) {
			return false;
		}
	}
}
