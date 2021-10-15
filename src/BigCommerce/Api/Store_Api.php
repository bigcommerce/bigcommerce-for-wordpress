<?php


namespace BigCommerce\Api;


use Firebase\JWT\JWT;

/**
 * Class Store_Api
 *
 * Handle retrieving information about store from APIv2
 *
 * @package BigCommerce\Api
 */
class Store_Api extends v2ApiAdapter {

    /**
     * Get customr login token by id
     *
     * @param $id
     * @param string $redirectUrl
     * @param string $requestIp
     * @param int $channel_id
     *
     * @return string
     *
     * @throws \Exception
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
     * Return the list of store analytics settings
     *
     * @return array|array[]
     */
	public function get_analytics_settings() {
		try {
			$settings = $this->getCollection( '/settings/analytics' );
			$settings = array_map( function ( Resource $resource ) {
				return get_object_vars( $resource->getUpdateFields() );
			}, $settings );
		} catch ( \Exception $e ) {
			$settings = [];
		}

		return $settings ?: [];
	}

    /**
     * Update store analytics setting by id
     *
     * @param $id
     * @param array $settings
     *
     * @return bool
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
     * Check if site wide https option is enabled in BC
     * @return false
     */
	public function get_sitewidehttps_enabled() {
		try {
			return $this->getResource('/store')->features->sitewidehttps_enabled;
		} catch ( \Exception $e ) {
			return false;
		}
	}

    /**
     * Get store domain
     *
     * @return false
     */
	public function get_domain() {
		try {
			return $this->getResource('/store')->domain;
		} catch ( \Exception $e ) {
			return false;
		}
	}
}
