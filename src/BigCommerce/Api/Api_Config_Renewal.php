<?php

namespace BigCommerce\Api;

use BigCommerce\Container\Api;
use BigCommerce\Container\Settings;
use BigCommerce\Settings\Sections\Api_Credentials;

class Api_Config_Renewal {

	private $config;

	public function __construct(Configuration $config ) {
		$this->config = $config;
	}

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
