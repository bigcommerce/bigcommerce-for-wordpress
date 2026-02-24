<?php

namespace BigCommerce\GraphQL;

use BigCommerce\Api\v3\ApiException;
use BigCommerce\Api\v3\Configuration;
use BigCommerce\Import\Processors\Headless_Product_Processor;
use BigCommerce\Logging\Error_Log;
use BigCommerce\Taxonomies\Channel\Channel;
use BigCommerce\Taxonomies\Channel\Connections;

/**
 * Handles interactions with the BigCommerce GraphQL API, including token management, making authenticated requests, and handling store-related information.
 */
class BaseGQL {
    /**
     * @var string GQL_BASE The base URL for GraphQL requests.
     */
    const GQL_BASE                = 'https://store-%s-%s.mybigcommerce.com/graphql';

    /**
     * @var string GQL_ACCEPT The accepted response format for GraphQL requests.
     */
    const GQL_ACCEPT              = 'application/json';

    /**
     * @var string TOKEN_EXPIRATION The transient key for token expiration time.
     */
    const TOKEN_EXPIRATION        = 'bigcommerce_gql_expire_at';

    /**
     * @var string GQL_TOKEN The transient key for storing the GraphQL token.
     */
    const GQL_TOKEN               = 'bigcommerce_gql_token';

    /**
     * @var string GQL_IMPERSONATION_TOKEN The transient key for storing the impersonation token.
     */
    const GQL_IMPERSONATION_TOKEN = 'bigcommerce_gql_im_token';

    /**
     * @var string GQL_BASE_URL The base URL for requesting a new GraphQL token.
     */
    const GQL_BASE_URL            = 'https://api.bigcommerce.com/stores/%s/v3/storefront/api-token';

    /**
     * @var string GQL_IMPERSONATION_URL The base URL for requesting an impersonation token.
     */
    const GQL_IMPERSONATION_URL   = 'https://api.bigcommerce.com/stores/%s/v3/storefront/api-token-customer-impersonation';

    /**
     * @var string $token The access token for GraphQL requests.
     */
    protected $token;

    /**
     * @var string $impersonation_token The impersonation token for GraphQL requests.
     */
    protected $impersonation_token;

    /**
     * @var \BigCommerce\Api\v3\Configuration $config The configuration object used for API requests.
     */
    protected $config;

    /**
     * Constructor.
     *
     * Initializes the BaseGQL instance with the provided configuration and retrieves the tokens.
     *
     * @param \BigCommerce\Api\v3\Configuration $config The configuration object used for API requests.
     */
	public function __construct( Configuration $config ) {
        $this->config = $config;

        $this->get_token();
        $this->get_impersonation_token();
    }

    /**
     * Retrieves the current channel ID from the site transient or connections.
     *
     * @return int The channel ID.
     */
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
     * Retrieves the current GraphQL token, requesting a new one if necessary.
     *
     * @return mixed The current token.
     */
    protected function get_token() {
        $this->token = get_site_transient( self::GQL_TOKEN );
        if ( $this->validate_token() ) {
            return $this->token;
        }

        $this->request_token();

        return $this->token;
    }

    /**
     * Retrieves the current impersonation token, requesting a new one if necessary.
     *
     * @return mixed The current impersonation token.
     */
    protected function get_impersonation_token() {
        $this->impersonation_token = get_site_transient( self::GQL_IMPERSONATION_TOKEN );
        if ( ! empty( $this->impersonation_token ) ) {
            return $this->impersonation_token;
        }

        $this->request_im_token();

        return $this->impersonation_token;
    }

    /**
     * Requests a new impersonation token from the API and stores it in a transient.
     *
     * @return void
     */
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
			/**
			 * Action to log messages during the BigCommerce logging process.
			 *
			 * This action is triggered when a log entry needs to be created. It logs the message, context, level, and path to the log file.
			 *
			 * @param string $level The log level (e.g., INFO, ERROR).
			 * @param string $message The log message to be recorded.
			 * @param array $context Additional context for the log entry.
			 * @param string $path The path where the log is being written.
			 * @return void
			 */
            do_action( 'bigcommerce/log', Error_Log::ERROR, __( 'Could not retrieve the token', 'bigcommerce' ), [
                'trace' => $e->getTraceAsString(),
            ] );

            return;
        }
    }

    /**
     * Requests a new GraphQL token from the API and stores it in a transient.
     *
     * @return void
     */
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

    /**
     * Makes a POST request to the GraphQL API with the provided data, headers, and URL.
     *
     * @param mixed  $data    The data to send in the request body.
     * @param array  $headers The headers for the request.
     * @param string $url     The URL for the request.
     *
     * @return mixed The parsed response data.
     * @throws ApiException If the request fails or the response is invalid.
     */
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

    /**
     * Parses the response from the GraphQL API.
     *
     * @param array $result The raw response data.
     *
     * @return mixed The parsed response data.
     * @throws ApiException If the response is invalid.
     */
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

    /**
     * Retrieves the store hash from the configuration host URL.
     *
     * @return string The store hash.
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
     * Retrieves the endpoint URL for the GraphQL request.
     *
     * @return string The full GraphQL endpoint URL.
     */
	protected function get_endpoint_url(): string {
		$store_hash = $this->get_store_hash();
		$channel_id = $this->get_channel_id();

		return sprintf( self::GQL_BASE, $store_hash, $channel_id );
	}

    /**
     * Retrieves the origin (site URL) for the request.
     *
     * @return string The origin URL.
     */
	protected function get_origin(): string {
		return site_url();
	}

    /**
     * Constructs the authorization bearer string for the request.
     *
     * @param bool $impersonation Whether to use the impersonation token.
     *
     * @return string The authorization header value.
     */
	protected function get_auth_bearer( $impersonation = false ): string {
		if ( $impersonation ) {
			return sprintf( 'Bearer %s', $this->get_impersonation_token() );
		}

		return sprintf( 'Bearer %s', $this->get_token() );
	}

    /**
     * Validates whether the current token is still valid and not expired.
     *
     * @return bool Whether the token is valid.
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
     * Constructs the headers for the GraphQL request, including authorization.
     *
     * @param bool $impersonation Whether to use the impersonation token.
     *
     * @return array The request headers.
     */
    protected function get_headers( $impersonation = false ): array {
        return [
            'Authorization' => $this->get_auth_bearer( $impersonation ),
            'Accept'        => self::GQL_ACCEPT,
            'Origin'        => $this->get_origin(),
        ];
    }
}
