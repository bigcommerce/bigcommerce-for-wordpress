<?php


namespace BigCommerce\Api;


class Configuration extends \BigCommerce\Api\v3\Configuration {
	protected $clientId;
	protected $clientSecret;

	/**
	 * @return string
	 */
	public function getClientId() {
		return $this->clientId;
	}

	/**
	 * @param string $clientId
	 */
	public function setClientId( $clientId ) {
		$this->clientId = $clientId;
	}

	/**
	 * @return string
	 */
	public function getClientSecret() {
		return $this->clientSecret;
	}

	/**
	 * @param string $clientSecret
	 */
	public function setClientSecret( $clientSecret ) {
		$this->clientSecret = $clientSecret;
	}


	/**
	 * Gets the default header
	 *
	 * @return array An array of default header(s)
	 */
	public function getDefaultHeaders() {
		return apply_filters( 'bigcommerce/api/default_headers', array_merge( $this->defaultHeaders, [
			'X-Auth-Client' => $this->clientId,
			'X-Auth-Token'  => $this->accessToken,
		] ) );
	}

}