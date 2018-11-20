<?php


namespace BigCommerce\Merchant;


use BigCommerce\Merchant\Models\Connect_Account_Request;

class Connect_Account {
	const CONNECT_ACTION = 'bigcommerce_connect_account';

	/**
	 * @var Onboarding_Api
	 */
	private $onboarding;

	/**
	 * @param Onboarding_Api $onboarding_api
	 */
	public function __construct( Onboarding_Api $onboarding_api ) {
		$this->onboarding = $onboarding_api;
	}

	/**
	 * Point the Connect Account button to the admin action URL we'll handle
	 *
	 * @param string $url
	 *
	 * @return string
	 * @filter bigcommerce/settings/connect_account_url
	 */
	public function connect_account_url( $url ) {
		$url = admin_url( 'admin-post.php' );
		$url = add_query_arg( [ 'action' => self::CONNECT_ACTION ], $url );
		$url = wp_nonce_url( $url, self::CONNECT_ACTION );

		return $url;
	}

	/**
	 * @return void
	 * @action admin_post_ . self::CONNECT_ACTION
	 */
	public function connect_account() {
		$tries     = 0;
		$max_tries = 5;

		$secret_key = wp_generate_password( 32, true, true );
		update_option( Onboarding_Api::AUTH_KEY, $secret_key, false );

		do {
			$tries ++;
			$store_id   = $this->generate_guid();

			update_option( Onboarding_Api::STORE_ID, $store_id );

			$request = new Connect_Account_Request( $secret_key, home_url( '/' ) );
			$response = $this->onboarding->connect_account( $store_id, $request );

			// If we get a 409 (conflict), try a few more times until we've generated a unique key.
			// Collision is extremely unlikely, given how the key is generated, but possible enough.
			$response_code = (int) wp_remote_retrieve_response_code( $response );
		} while ( $response_code === 409 && $tries < $max_tries );

		try {
			if ( is_wp_error( $response ) ) {
				add_settings_error( self::CONNECT_ACTION, $response->get_error_code(), $response->get_error_message() );
				throw new \RuntimeException();
			}

			if ( $response_code < 200 || $response_code >= 400 ) {
				add_settings_error( self::CONNECT_ACTION, $response_code, __( 'We encountered an unexpected error creating your account. Please try again.', 'bigcommerce' ) );
				throw new \RuntimeException();
			}
			$redirect_url = apply_filters( 'bigcommerce/onboarding/success_redirect', admin_url() );
		} catch ( \RuntimeException $e ) {
			delete_option( Onboarding_Api::STORE_ID );
			set_transient( 'settings_errors', get_settings_errors(), 30 );
			$redirect_url = apply_filters( 'bigcommerce/onboarding/error_redirect', admin_url() );
		}

		wp_safe_redirect( esc_url_raw( $redirect_url ), 303 );
		exit();
	}

	/**
	 * Create a random GUID to use as a store identifier when
	 * connecting to an existing store where we don't know its
	 * store ID.
	 *
	 * @return string
	 */
	private function generate_guid() {
		$charid = md5( uniqid( rand(), true ) );
		$hyphen = '-';
		$uuid   = 'wp' // All our pseudo-IDs will start with WP for easy identification. No functionality depends on this.
		          . substr( $charid, 2, 8 ) . $hyphen
		          . substr( $charid, 8, 4 ) . $hyphen
		          . substr( $charid, 12, 4 ) . $hyphen
		          . substr( $charid, 16, 4 ) . $hyphen
		          . substr( $charid, 20, 12 );

		return $uuid;
	}
}