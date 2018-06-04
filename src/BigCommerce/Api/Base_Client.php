<?php

namespace BigCommerce\Api;

use BigCommerce\Api\v3\ApiClient;

class Base_Client extends ApiClient {

	/**
	 * Configuration
	 *
	 * @var Configuration
	 */
	protected $config;

	/**
	 * Constructor of the class
	 *
	 * @param Configuration $config config for this ApiClient
	 */
	public function __construct( Configuration $config = null ) {
		parent::__construct( $config );
	}

	/**
	 * Get the config
	 *
	 * @return Configuration
	 */
	public function getConfig() {
		return $this->config;
	}
}