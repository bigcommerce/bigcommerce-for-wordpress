<?php


namespace BigCommerce\Container;


use BigCommerce\Api\Caching_Client;
use BigCommerce\Api\Null_Client;
use BigCommerce\Api\Configuration;
use BigCommerce\Api\Request_Headers;
use BigCommerce\Api_Factory;
use BigCommerce\Settings\Sections\Api_Credentials;
use Pimple\Container;

class Api extends Provider {
	const CONFIG_COMPLETE = 'api.configuration.complete';

	const CLIENT        = 'api.client';
	const CONFIG        = 'api.configuration';
	const CLIENT_ID     = 'api.client_id';
	const CLIENT_SECRET = 'api.client_secret';
	const ACCESS_TOKEN  = 'api.access_token';
	const HOST          = 'api.host';
	const FACTORY       = 'api.factory';
	const TIMEOUT       = 'api.timeout';
	const HEADERS       = 'api.headers';

	public function register( Container $container ) {
		$container[ self::CLIENT ] = function ( Container $container ) {
			/** @var Configuration $config */
			$config = $container[ self::CONFIG ];
			if ( $config->getHost() == '' || count( array_filter( $config->getDefaultHeaders() ) ) < 2 ) {
				return new Null_Client( $config );
			}

			return new Caching_Client( $config );
		};

		$container[ self::CONFIG ] = function ( Container $container ) {
			$config = new Configuration();
			$config->setHost( untrailingslashit( $container[ self::HOST ] ) );
			$config->setClientId( $container[ self::CLIENT_ID ] );
			$config->setAccessToken( $container[ self::ACCESS_TOKEN ] );
			$config->setClientSecret( $container[ self::CLIENT_SECRET ] );
			$config->setCurlTimeout( $container[ self::TIMEOUT ] );

			/**
			 * Filter the API connection configuration object
			 *
			 * @param Configuration $config
			 */
			return apply_filters( 'bigcommerce/api/config', $config );
		};

		$container[ self::CLIENT_ID ] = function ( Container $container ) {
			$env = bigcommerce_get_env( 'BIGCOMMERCE_CLIENT_ID' );

			return $env ?: get_option( Api_Credentials::OPTION_CLIENT_ID, '' );
		};

		$container[ self::CLIENT_SECRET ] = function ( Container $container ) {
			$env = bigcommerce_get_env( 'BIGCOMMERCE_CLIENT_SECRET' );

			return $env ?: get_option( Api_Credentials::OPTION_CLIENT_SECRET, '' );
		};

		$container[ self::ACCESS_TOKEN ] = function ( Container $container ) {
			$env = bigcommerce_get_env( 'BIGCOMMERCE_ACCESS_TOKEN' );

			return $env ?: get_option( Api_Credentials::OPTION_ACCESS_TOKEN, '' );
		};

		$container[ self::HOST ] = function ( Container $container ) {
			$env = bigcommerce_get_env( 'BIGCOMMERCE_API_URL' );

			return $env ?: get_option( Api_Credentials::OPTION_STORE_URL, '' );
		};

		$container[ self::TIMEOUT ] = function ( Container $container ) {
			/**
			 * Filter the API connection timeout
			 *
			 * @param int $timeout The timeout in seconds
			 */
			return apply_filters( 'bigcommerce/api/timeout', 15 );
		};

		$container[ self::FACTORY ] = function ( Container $container ) {
			return new Api_Factory( $container[ self::CLIENT ] );
		};

		$container[ self::HEADERS ] = function ( Container $container ) {
			return new Request_Headers();
		};

		$container[ self::CONFIG_COMPLETE ] = function( Container $container ) {
			$credentials = [
				$container[ self::CLIENT_ID ],
				$container[ self::ACCESS_TOKEN ],
				$container[ self::HOST ],
				// not including Client Secret here, because that's stored in the auth connector
			];
			return ( count( array_filter( $credentials ) ) === count( $credentials ) );
		};

		add_filter( 'bigcommerce/plugin/credentials_set', $this->create_callback( 'credentials_set', function ( $set ) use ( $container ) {
			return $container[ self::CONFIG_COMPLETE ];
		} ), 10, 1 );

		add_filter( 'bigcommerce/api/default_headers', $this->create_callback( 'request_headers', function ( $headers ) use ( $container ) {
			return $container[ self::HEADERS ]->add_plugin_info_headers( $headers );
		} ), 10, 1 );

		add_action( 'plugins_loaded', $this->create_callback( 'configure_v2_client', function () use ( $container ) {
			// configure the static properties of the global v2 client
			$v3_url = untrailingslashit( $container[ self::HOST ] );
			preg_match( '#stores/([^\/]+)/#', $v3_url, $matches );
			if ( empty( $matches[ 1 ] ) ) {
				$store_hash = '';
			} else {
				$store_hash = $matches[ 1 ];
			}
			\Bigcommerce\Api\Client::configure( [
				'client_id'     => $container[ self::CLIENT_ID ],
				'auth_token'    => $container[ self::ACCESS_TOKEN ],
				'client_secret' => $container[ self::CLIENT_SECRET ],
				'store_hash'    => $store_hash,
			] );
		} ), 10, 0 );
	}
}
