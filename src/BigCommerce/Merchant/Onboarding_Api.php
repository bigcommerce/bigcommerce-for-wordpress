<?php


namespace BigCommerce\Merchant;


use BigCommerce\Merchant\Models\Connect_Account_Request;
use BigCommerce\Merchant\Models\Create_Account_Request;
use BigCommerce\Merchant\Models\Customer_Login_Request;
use Firebase\JWT\JWT;

class Onboarding_Api {
	const AUTH_KEY   = 'bigcommerce_oauth_broker_secret';
	const ACCOUNT_ID = 'bigcommerce_account_id';
	const STORE_ID   = 'bigcommerce_store_id';

	/**
	 * @var string
	 */
	private $base_url;

	private $timeout = 30;

	/**
	 * @param string $base_url
	 */
	public function __construct( $base_url ) {
		$this->base_url = untrailingslashit( esc_url_raw( $base_url ) );
	}

	public function create_account( Create_Account_Request $request ) {
		$request = json_decode( wp_json_encode( $request ), true ); // convert to array

		return wp_remote_post( $this->base_url . '/stores', [
			'body'    => $request,
			'timeout' => $this->timeout,
		] );
	}

	public function connect_account( $store_id, Connect_Account_Request $request ) {
		$request = json_decode( wp_json_encode( $request ), true ); // convert to array

		return wp_remote_post( sprintf( '%s/stores/%s', $this->base_url, $store_id ), [
			'body'    => $request,
			'timeout' => $this->timeout,
			'method'  => 'PUT',
		] );
	}

	public function status( $store_id ) {
		$token        = $this->build_auth_token( $store_id );
		$request_url  = sprintf( '%s/stores/%s/status', $this->base_url, $store_id );
		$request_body = [
			'token' => $token,
		];

		return wp_remote_get( $request_url, [
			'body'    => $request_body,
			'timeout' => $this->timeout,
		] );
	}

	/**
	 * Build the auth token to make an authenticated request to the connector API
	 *
	 * @param $store_id
	 *
	 * @return string The auth token, a JWT
	 */
	private function build_auth_token( $store_id ) {
		$secret = get_option( self::AUTH_KEY, '' );
		if ( empty( $secret ) || empty( $store_id ) ) {
			throw new \RuntimeException( __( 'Store ID and/or secret key are not set. Try creating a store first.', 'bigcommerce' ) );
		}
		$payload = [
			'sub' => $store_id,
			'iss' => home_url( '/' ),
			'iat' => time(),
		];

		return JWT::encode( $payload, $secret, 'HS256' );
	}

	/**
	 * Get the URL to launch the app installation process with BigCommerce
	 *
	 * @param string $store_id
	 *
	 * @return string
	 */
	public function installation_url( $store_id ) {
		$token = $this->build_auth_token( $store_id );
		$url   = sprintf( '%s/stores/%s/connect', $this->base_url, $store_id );
		$url   = add_query_arg( [ 'token' => $token ], $url );

		return apply_filters( 'bigcommerce/oauth_connector/installation_url', $url );
	}

	/**
	 * @param string                 $store_id
	 * @param string                 $customer_id
	 * @param Customer_Login_Request $request
	 *
	 * @return string
	 * @throws \RuntimeException
	 */
	public function customer_login_token( $store_id, $customer_id, Customer_Login_Request $request ) {
		$request            = json_decode( wp_json_encode( $request ), true ); // convert to array
		$request[ 'token' ] = $this->build_auth_token( $store_id );
		$url                = sprintf( '%s/stores/%s/customers/%s/login-token', $this->base_url, $store_id, $customer_id );
		$response           = wp_remote_post( $url, [
			'body'    => array_filter( $request ),
			'timeout' => $this->timeout,
		] );
		if ( is_wp_error( $response ) || wp_remote_retrieve_response_code( $response ) !== 200 ) {
			return '';
		}

		return wp_remote_retrieve_body( $response );
	}
}