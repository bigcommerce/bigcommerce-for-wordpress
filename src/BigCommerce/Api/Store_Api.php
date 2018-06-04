<?php


namespace BigCommerce\Api;


use Firebase\JWT\JWT;

class Store_Api extends v2ApiAdapter {

	public function getCustomerLoginToken( $id, $redirectUrl = '', $requestIp = '' ) {
		$config    = $this->apiClient->getConfig();
		$client_id = $config->getClientId();
		$secret    = $config->getClientSecret();
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

		return JWT::encode( $payload, $secret, 'HS256' );
	}

	private function get_server_time() {
		$offset = get_transient( 'bigcommerce_time_offset' );
		if ( $offset === false ) {
			$offset = $this->update_server_time();
		}

		return time() + $offset;
	}

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

}