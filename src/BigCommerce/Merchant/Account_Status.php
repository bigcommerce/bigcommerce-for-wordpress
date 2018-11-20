<?php


namespace BigCommerce\Merchant;


use BigCommerce\Settings\Sections\Api_Credentials;

class Account_Status {

	const STATUS_AJAX = 'bigcommerce_account_status';


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
	 * Render the placeholder div with the JS data for account status ajax requests
	 *
	 * @return void
	 * @action bigcommerce/settings/after_content/page= . Pending_Account_Screen::NAME
	 */
	public function render_status_placeholder() {
		$ajax_url = admin_url( 'admin-ajax.php' );
		printf( '<div data-js="account-pending-status" data-url="%s"></div>', esc_url( $ajax_url ) );
	}

	/**
	 * Handle the ajax request to update the store status
	 *
	 * @return void
	 * @action wp_ajax_ . self::STATUS_AJAX
	 */
	public function handle_account_status_request() {
		$this->validate_ajax_nonce( $_REQUEST );

		$store = get_option( Onboarding_Api::STORE_ID, 0 );
		try {
			$response = $this->onboarding->status( $store );
		} catch ( \RuntimeException $e ) {
			wp_send_json_error( [
				'code'    => 'missing_credentials',
				'message' => $e->getMessage(),
			], 400 );
			exit();
		}


		$response_code = (int) wp_remote_retrieve_response_code( $response );
		$response_body = json_decode( wp_remote_retrieve_body( $response ), true );
		if ( $response_code >= 400 && $response_code < 500 ) {
			delete_option( Onboarding_Api::ACCOUNT_ID );
			delete_option( Onboarding_Api::STORE_ID );
			add_settings_error( self::STATUS_AJAX, $response_code, __( 'We encountered an unexpected error connecting your account. Please try again.', 'bigcommerce' ) );
			set_transient( 'settings_errors', get_settings_errors(), 30 );
			wp_send_json_error( [
				'code'          => 'not_found',
				'message'       => __( 'There was an error locating your account. Please refresh this page and try connecting again.', 'bigcommerce' ),
				'data'          => wp_remote_retrieve_body( $response ),
				'response_code' => $response_code,
				'redirect'      => apply_filters( 'bigcommerce/onboarding/error_redirect', admin_url() ),
			], 200 );
			exit();
		}
		if ( $response_code < 200 || $response_code >= 400 || empty( $response_body ) || empty( $response_body[ 'status' ] ) ) {
			wp_send_json_error( [
				'code'          => 'bad_gateway',
				'message'       => __( 'There was an error retrieving your account status.', 'bigcommerce' ),
				'data'          => wp_remote_retrieve_body( $response ),
				'response_code' => $response_code,
			], 502 );
			exit();
		}

		switch ( $response_body[ 'status' ] ) {
			case 'provisioning':
				wp_send_json_success( [
					'status'  => 'processing',
					'message' => __( 'Still working...', 'bigcommerce' ),
				] );
				exit();
			case 'temporary':
				wp_send_json_success( [
					'popup' => esc_url_raw( $this->onboarding->installation_url( $store ) ),
				] );
				exit();
			case 'active':
				$this->save_store_hash( $response_body[ 'store_hash' ] );
				wp_send_json_success( [
					'popup' => esc_url_raw( $this->onboarding->installation_url( $store ) ),
				] );
				exit();
			case 'authenticated':
				if ( empty( $response_body[ 'client_id' ] ) || empty( $response_body[ 'store_hash' ] ) || empty( $response_body[ 'oauth_token' ] ) ) {
					wp_send_json_error( [
						'code'          => 'bad_gateway',
						'message'       => __( 'There was an error retrieving your account status.', 'bigcommerce' ),
						'data'          => wp_remote_retrieve_body( $response ),
						'response_code' => $response_code,
					], 502 );
					exit();
				}

				$this->save_store_hash( $response_body[ 'store_hash' ] );
				$this->save_auth_credentials( $response_body[ 'client_id' ], $response_body[ 'oauth_token' ] );

				$redirect_url = apply_filters( 'bigcommerce/onboarding/success_redirect', admin_url() );

				wp_send_json_success( [
					'redirect' => $redirect_url,
				] );
				exit();
			default:
				wp_send_json_error( [
					'code'    => 'bad_gateway',
					'message' => __( 'Invalid response from the API server.', 'bigcommerce' ),
				], 502 );
				exit();
		}

	}

	/**
	 * Validate the nonce for an ajax status request
	 *
	 * @param array $request
	 *
	 * @return void
	 */
	private function validate_ajax_nonce( $request ) {
		if ( empty( $request[ '_wpnonce' ] ) || ! wp_verify_nonce( $request[ '_wpnonce' ], self::STATUS_AJAX ) ) {
			wp_send_json_error( [
				'code'    => 'invalid_nonce',
				'message' => __( 'Invalid request.', 'bigcommerce' ),
			] );
			exit();
		}
	}

	/**
	 * Save the store's hash to the DB
	 *
	 * @param string $hash
	 *
	 * @return void
	 */
	private function save_store_hash( $hash ) {
		update_option( Api_Credentials::OPTION_STORE_URL, sprintf( 'https://api.bigcommerce.com/stores/%s/v3/', $hash ) );
	}

	/**
	 * Save the client ID and auth token to the DB
	 *
	 * @param string $client_id
	 * @param string $auth_token
	 *
	 * @return void
	 */
	private function save_auth_credentials( $client_id, $auth_token ) {
		update_option( Api_Credentials::OPTION_CLIENT_ID, $client_id );
		update_option( Api_Credentials::OPTION_ACCESS_TOKEN, $auth_token );
	}

}
