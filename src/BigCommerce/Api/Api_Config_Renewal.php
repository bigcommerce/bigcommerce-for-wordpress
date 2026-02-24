<?php

namespace BigCommerce\Api;

use BigCommerce\Container\Api;
use BigCommerce\Container\Settings;
use BigCommerce\Settings\Sections\Api_Credentials;

/**
 * Class Api_Config_Renewal
 *
 * Handles the renewal of API configuration settings.
 *
 * This class is responsible for updating the configuration of the BigCommerce API 
 * based on new credentials (store URL, client ID, client secret, and access token). 
 * After updating the configuration, it reconfigures the BigCommerce API client 
 * with the new credentials.
 *
 * @package BigCommerce\Api
 */
class Api_Config_Renewal {

    /**
     * @var Configuration
     */
    private $config;

    /**
     * Api_Config_Renewal constructor.
     *
     * Initializes the API configuration renewal process.
     *
     * @param Configuration $config The configuration object used to store and manage API credentials.
     */
    public function __construct(Configuration $config ) {
        $this->config = $config;
    }

    /**
     * Renews the API configuration based on the provided option and value.
     *
     * This function updates the API configuration by setting the new value for 
     * the specified option (store URL, client ID, client secret, or access token). 
     * After updating the configuration, it reconfigures the BigCommerce API client 
     * with the new credentials.
     *
     * @param string $option The configuration option to renew (e.g., 'store_url', 'client_id').
     * @param string $value The new value for the configuration option.
     * 
     * @return Configuration The updated configuration object.
     */
    public function renewal_config( $option, $value ) {
        switch ( $option ) {
            case Api_Credentials::OPTION_STORE_URL:
                $this->config->setHost( $value );
                break;
            case Api_Credentials::OPTION_CLIENT_ID:
                $this->config->setClientId( $value );
                break;
            case Api_Credentials::OPTION_CLIENT_SECRET:
                $this->config->setClientSecret( $value );
                break;
            case Api_Credentials::OPTION_ACCESS_TOKEN:
                $this->config->setAccessToken( $value );
                break;
        }

        $hash = bigcommerce()->container()[ Settings::CREDENTIALS_SCREEN ]->get_store_hash( $this->config->getHost() );

        \Bigcommerce\Api\Client::configure( [
                'client_id'     => $this->config->getClientId(),
                'auth_token'    => $this->config->getAccessToken(),
                'client_secret' => $this->config->getClientSecret(),
                'store_hash'    => $hash,
        ] );

        return $this->config;
    }
	
}
