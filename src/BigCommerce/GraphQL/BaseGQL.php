<?php

namespace BigCommerce\GraphQL;

use BigCommerce\Api\v3\ApiException;
use BigCommerce\Api\v3\Configuration;
use BigCommerce\Import\Processors\Headless_Product_Processor;
use BigCommerce\Logging\Error_Log;
use BigCommerce\Taxonomies\Channel\Channel;
use BigCommerce\Taxonomies\Channel\Connections;

class BaseGQL {
	const GQL_BASE                = 'https://store-%s-%s.mybigcommerce.com/graphql';
	const GQL_ACCEPT              = 'application/json';
	const TOKEN_EXPIRATION        = 'bigcommerce_gql_expire_at';
	const GQL_TOKEN               = 'bigcommerce_gql_token';
	const GQL_IMPERSONATION_TOKEN = 'bigcommerce_gql_im_token';
	const GQL_BASE_URL            = 'https://api.bigcommerce.com/stores/%s/v3/storefront/api-token';
	const GQL_IMPERSONATION_URL   = 'https://api.bigcommerce.com/stores/%s/v3/storefront/api-token-customer-impersonation';

	protected $token;
	protected $impersonation_token;

	protected $config;

	/**
	 * @param \BigCommerce\Api\v3\Configuration $config
	 */
	public function __construct( Configuration $config ) {
		$this->config = $config;

		$this->get_token();
		$this->get_impersonation_token();
	}

	protected function get_channel_id() {
		$term_id = get_site_transient( sprintf( '%s', Headless_Product_Processor::HEADLESS_CHANNEL ) );
		if ( ! empty( $term_id ) ) {
			return ( int ) get_term_meta( $term_id, Channel::CHANNEL_ID, true);
		}
		$connections = new Connections();
		$channel     = $connections->current();
		$channel_id  = get_term_meta( $channel->term_id, Channel::CHANNEL_ID, true);

		return ( int ) $channel_id;
	}

	/**
	 * @return mixed
	 */
	protected function get_token() {
		$this->token = get_site_transient( self::GQL_TOKEN );
		if ( $this->validate_token() ) {
			return $this->token;
		}

		$this->request_token();

		return $this->token;
	}

	protected function get_impersonation_token() {
		$this->impersonation_token = get_site_transient( self::GQL_IMPERSONATION_TOKEN );
		if ( ! empty( $this->impersonation_token ) ) {
			return $this->impersonation_token;
		}

		$this->request_im_token();

		return $this->impersonation_token;
	}

	protected function request_im_token() {
		$expiration = get_option( self::TOKEN_EXPIRATION, 12 * HOUR_IN_SECONDS );
		$expires_at = time() + (int) $expiration;
		$post_body  = [
			'channel_id' => $this->get_channel_id(),
			'expires_at' => $expires_at,
		];
		$url        = sprintf( self::GQL_IMPERSONATION_URL, $this->get_store_hash() );

		try {
			$token    = $this->config->getAccessToken();
			$headers  = [ 'X-Auth-Token' => $token, ];
			$response = $this->make_request( $post_body, $headers, $url );

			$this->impersonation_token = $response->data->token;
			set_site_transient( self::GQL_IMPERSONATION_TOKEN, $response->data->token, $expiration );
		} catch ( \Exception $e ) {
			do_action( 'bigcommerce/log', Error_Log::ERROR, __( 'Could not retrieve the token', 'bigcommerce' ), [
					'trace' => $e->getTraceAsString(),
			] );

			return;
		}
	}

	protected function request_token() {
		$expiration = get_option( self::TOKEN_EXPIRATION, 12 * HOUR_IN_SECONDS );
		$expires_at = time() + (int) $expiration;
		$post_body  = [
			'channel_id'           => $this->get_channel_id(),
			'expires_at'           => $expires_at,
			'allowed_cors_origins' => [ site_url() ],
		];
		$url        = sprintf( self::GQL_BASE_URL, $this->get_store_hash() );

		try {
			$token       = $this->config->getAccessToken();
			$headers     = [ 'X-Auth-Token' => $token, ];
			$response    = $this->make_request( $post_body, $headers, $url );
			$this->token = $response->data->token;

			set_site_transient( self::GQL_TOKEN, $this->token, $expiration );
		} catch ( \Exception $e ) {
			do_action( 'bigcommerce/log', Error_Log::ERROR, __( 'Could not retrieve the token', 'bigcommerce' ), [
				'trace' => $e->getTraceAsString(),
			] );

			return;
		}
	}

	public function make_request($data, $headers = [], $url = '') {
		if ( empty( $url ) ) {
			$url = $this->get_endpoint_url();
		}

		if ( empty( $headers ) ) {
			$headers = $this->get_headers();
		}

		$default_headers = [
			'Content-Type' => 'application/json',
		];

		$headers = array_merge( $default_headers, $headers );
		$data    = json_encode( $data );

		$result = wp_remote_post( $url, [
			'headers'    => $headers,
			'body'       => $data,
			'user-agent' => $this->config->getUserAgent(),
		] );

		if ( is_wp_error( $result ) ) {
			throw new ApiException( $result->get_error_message() );
		}

		return $this->parse_response( $result );
	}

	protected function parse_response( $result ) {
		$data = json_decode( $result['body'] );

		if ( $result['response']['code'] !== 200 ) {
			if ( json_last_error() > 0 ) {
				$message = $result['body'];
			} else {
				$message = [];

				foreach ( $data->errors as $error ) {
					switch( gettype( $error ) ) {
						case 'array':
							$message[] = $error['message'];
							break;
						case 'object':
							$message[] = $error->message;
							break;
						default:
							$message[] = $error;
							break;
					}
				}
			}

			throw new ApiException( implode( "\n", $message ), $result['response']['code'] );
		}

		 // if response is a string, return body
		if ( json_last_error() > 0 ) {
			$data = $result['body'];
		}

		return $data;
	}

	/** Retrieve store hash from host url
	 *
	 * @return string
	 */
	protected function get_store_hash(): string {
		$host = $this->config->getHost();
		preg_match( '#stores/([^\/]+)/#', $host, $matches );
		if ( empty( $matches[1] ) ) {
			return '';
		}

		return $matches[1];
	}

	/**
	 * Get endpoint url for request
	 *
	 * @return string
	 */
	protected function get_endpoint_url(): string {
		$store_hash = $this->get_store_hash();
		$channel_id = $this->get_channel_id();

		return sprintf( self::GQL_BASE, $store_hash, $channel_id );
	}

	/**
	 * Return WordPress origin url
	 *
	 * @return string
	 */
	protected function get_origin(): string {
		return site_url();
	}

	/**
	 * Get auth bearer string
	 *
	 * @return string
	 */
	protected function get_auth_bearer( $impersonation = false ): string {
		if ( $impersonation ) {
			return sprintf( 'Bearer %s', $this->get_impersonation_token() );
		}

		return sprintf( 'Bearer %s', $this->get_token() );
	}

	/**
	 * Check if token has not expired and has valid cors
	 *
	 * @return bool
	 */
	protected function validate_token(): bool {
		if ( empty( $this->token ) ) {
			return false;
		}

		$parts = explode( '.', $this->token );

		if ( empty( $parts ) ) {
			return false;
		}

		$payload = json_decode( base64_decode( $parts[1] ) );
		if ( empty( $payload ) ) {
			return false;
		}

		$expiration = ( int ) $payload->eat;
		$is_expired = ( $expiration - time() ) < 0;

		return ! $is_expired && in_array( site_url(), $payload->cors );
	}

	/**
	 * Return request headers
	 *
	 * @return string[]
	 */
	protected function get_headers( $impersonation = false ): array {
		return [
			'Authorization' => $this->get_auth_bearer( $impersonation ),
			'Accept'        => self::GQL_ACCEPT,
			'Origin'        => $this->get_origin(),
		];
	}
}
