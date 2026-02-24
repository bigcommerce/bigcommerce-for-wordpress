<?php

namespace BigCommerce\Api;

use BigCommerce\Api\v3\ApiClient;

/**
 * Base API client class that extends the ApiClient and manages the configuration 
 * of the API client. This class is designed to handle the configuration for 
 * API communication and can be extended for specific API clients.
 *
 * @package BigCommerce\Api
 */
class Base_Client extends ApiClient {

    /**
     * Configuration for the API client
     *
     * @var Configuration
     */
    protected $config;

    /**
     * Constructor for the Base_Client class
     *
     * Initializes the API client with the provided configuration. If no configuration 
     * is provided, it will use the default configuration.
     *
     * @param Configuration|null $config The configuration for this ApiClient.
     */
    public function __construct( Configuration $config = null ) {
        parent::__construct( $config );
    }

    /**
     * Gets the configuration of the API client
     *
     * Returns the configuration object used by the API client.
     *
     * @return Configuration Returns the Configuration object used by this API client.
     */
    public function getConfig() {
        return $this->config;
    }
}