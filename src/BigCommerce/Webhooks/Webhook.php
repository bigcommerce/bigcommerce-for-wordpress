<?php
/**
 * Abstract class providing an interface for setting up BigCommerce webhooks.
 *
 * @package BigCommerce
 */

namespace BigCommerce\Webhooks;

use BigCommerce\Api\Webhooks_Api;

/**
 * Sets up a webhook in the BigCommerce API to send event-based requests to the WP site.
 */
abstract class Webhook {
	const VERSION               = 1;
	const NAME                  = '';
	const SCOPE                 = '';
	const AUTH_HEADER           = 'X-WP-BigCommerce-Webhook-Auth-Header';
	const INPUT_AUTH_HEADER     = 'HTTP_X_WP_BIGCOMMERCE_WEBHOOK_AUTH_HEADER';
	const PASSWORD_ERROR_CODE   = 'bigcommerce_webhook_password_error';
	const VALIDATION_ERROR_CODE = 'bigcommerce_webhook_validation_error';
	const WEBHOOKS_OPTION       = 'bigcommerce_webhooks';
	const AUTH_KEY_OPTION       = 'bigcommerce_webhook_key';

	/**
	 * @var Webhooks_Api
	 */
	private $api_client;


	/**
	 * Webhook constructor
	 *
	 * @param Webhooks_Api $api_client The client for making requests.
	 */
	public function __construct( Webhooks_Api $api_client ) {
		$this->api_client = $api_client;
	}

	public function get_name() {
		return static::NAME;
	}

	/**
	 * Returns the value of the auth header.
	 *
	 * @return string|bool|null The value, or false or null if filter_input fails.
	 */
	public function get_auth_header() {
		// On some envs filter_input(https://bugs.php.net/bug.php?id=49184) may return NULL value even if variable exists
		// In order to prevent the issue we use filter_var for a $_SERVER variable
		return filter_var( $_SERVER[ self::INPUT_AUTH_HEADER ], FILTER_UNSAFE_RAW );
	}

	/**
	 * Sends a request to BigCommerce to create a webhook.
	 *
	 * @param array $args Request arguments.
	 *
	 * @return array Webhook data or an error response on failure.
	 */
	public function create( $args ) {
		return $this->api_client->createWebhook( $args );
	}

	/**
	 * Check by destination and scope if webhook is already added to BigCommerce.
	 * Returns the id of the webhook
	 *
	 * @return mixed|null
	 */
	public function is_webhook_exist() {
		$webhooks = $this->api_client->listWebhooks();

		$scope = $this->scope();
		$destination = $this->destination();

		$found_hooks = array_map(function ( $hook ) use ( $scope, $destination ) {
			$exists = $hook->scope === $scope && $hook->destination === $destination;
			$is_active = $hook->is_active === true;

			return $exists && $is_active ? $hook->id : false;
		}, $webhooks );

		if ( empty( $found_hooks ) ) {
			return null;
		}

		// Filter out empty values
		$filtered_result = array_filter( $found_hooks );

		return array_pop( $filtered_result );
	}

	/**
	 * Sends a request to the BC API to update a webhook. Creates it if it doesn't exist.
	 */
	public function update() {

		/**
		 * Create a password for authenticating the incoming request from BigCommerce.
		 */
		$password = $this->generate_password();

		$existing_webhook_id = $this->is_webhook_exist();

		/**
		 * Check if webhook exists in BigCommerce
		 */
		if ( ! empty( $existing_webhook_id ) ) {
			$args = [
				'headers' => [ self::AUTH_HEADER => $password ],
			];

			$this->update_webhook( $existing_webhook_id, $args );

			do_action( 'bigcommerce/webhooks/webhook_updated', intval( $existing_webhook_id ), static::NAME, $this->scope() );

			return $existing_webhook_id;
		}

		$args = [
			'headers'     => [ self::AUTH_HEADER => $password ],
			'scope'       => $this->scope(),
			'destination' => $this->destination(),
			'is_active'   => true,
		];

		/**
		 * Filter the arguments sent to the BigCommerce API to register a webhook
		 */
		$args = apply_filters( 'bigcommerce/webhooks/registration_args', $args, $this );

		$result = (array) $this->create( $args );

		if ( empty( $result[ 'id' ] ) ) {
			/**
			 * Fires after webhook update failed.
			 *
			 * @param Webhook Webhook Webhook class.
			 * @param array $result Result.
			 */
			do_action( 'bigcommerce/webhooks/update_failed', $this, $result );
		}

		$webhooks            = get_option( self::WEBHOOKS_OPTION, [] );
		$previous_webhook_id = array_key_exists( static::NAME, $webhooks ) ? absint( $webhooks[ static::NAME ] ) : 0;

		// Save the returned webhook ID as an option to help with cleanup later.
		$webhooks[ static::NAME ] = $result[ 'id' ];
		update_option( self::WEBHOOKS_OPTION, $webhooks );

		if ( $previous_webhook_id ) {
			// Clean up obsolete web hook.
			$this->delete( $previous_webhook_id );
		}


		/**
		 * Fires when a webhook is added to the BigCommerce database.
		 *
		 * @param int Webhook ID.
		 * @param string Webhook action name.
		 * @param string Webhook scope.
		 */
		do_action( 'bigcommerce/webhooks/webhook_updated', intval( $result[ 'id' ] ), static::NAME, $this->scope() );

		return $result[ 'id' ];
	}

	/**
	 * Send API request to update the webhook data
	 *
	 * @param $id
	 * @param $data
	 */
	public function update_webhook( $id, $data) {
		$this->api_client->updateWebhook( $id, $data );
	}

	public function destination() {
		return sprintf( '%s/bigcommerce/webhook/%s', home_url(), static::NAME );
	}

	public function scope(  ) {
		return static::SCOPE;
	}

	private function generate_password() {
		$option = get_option( self::AUTH_KEY_OPTION, '' );
		if ( empty( $option ) ) {
			$option = wp_generate_password( 32 );
			update_option( self::AUTH_KEY_OPTION, $option );
		}

		return md5( $option . static::NAME );
	}

	/**
	 * Deletes a webhook from the BigCommerce database.
	 *
	 * @param int $webhook_id The BC ID for the webhook entry.
	 */
	public function delete( $webhook_id ) {
		// deleteWebhook returns the deleted webhook on success.
		$result = (array) $this->api_client->deleteWebhook( $webhook_id );

		if ( empty( $result[ 'id' ] ) ) {
			/**
			 * Fires after webhook delete failed.
			 *
			 * @param Webhook Webhook Webhook class.
			 * @param array $result Result.
			 */
			do_action( 'bigcommerce/webhooks/delete_failed', $this, $result );
		}

		/**
		 * Fires when a webhook is deleted from the BigCommerce database.
		 *
		 * @param int Webhook ID.
		 * @param string Webhook action name.
		 * @param string Webhook scope.
		 */
		do_action( 'bigcommerce/webhooks/webhook_deleted', $result[ 'id' ], static::NAME, $this->scope() );

		return $result[ 'id' ];
	}

	/**
	 * Validates an incoming request.
	 *
	 * @param array            $request  Request data.
	 * @param string|bool|null $password The password to authenticate with.
	 *
	 * @return bool|\WP_Error True on validation or a WP_Error if the request isn't valid.
	 */
	public function validate( $request, $password = null ) {
		if ( ! $password ) {
			$password = $this->get_auth_header();
		}

		if ( ! $password || $this->generate_password() !== $password ) {
			return new \WP_Error(
				static::PASSWORD_ERROR_CODE,
				__( 'Password header does not match.', 'bigcommerce' )
			);
		}

		if ( ! is_array( $request ) || ! isset( $request[ 'data' ][ 'type' ] ) || ! isset( $request[ 'data' ][ 'id' ] ) ) {
			return new \WP_Error(
				static::VALIDATION_ERROR_CODE,
				__( 'Webhook request data is invalid.', 'bigcommerce' )
			);
		}

		return true;
	}

	/**
	 * Get JSON input submitted from BigCommerce.
	 *
	 * @return array JSON data converted to an array.
	 */
	public function get_webhook_payload() {
		$json_content = file_get_contents( 'php://input' );

		return json_decode( $json_content, true );
	}

	/**
	 * Handles a webhook request.
	 */
	public function receive() {
		$request = $this->get_webhook_payload();

		$validates = $this->validate( $request );

		if ( is_wp_error( $validates ) ) {
			wp_send_json_error( $validates, 400 );
		}

		$this->trigger_action( $request );

		wp_send_json_success();
	}

	/**
	 * Triggers an action based on the webhook type and the request payload
	 *
	 * @param array $request
	 *
	 * @return void
	 */
	abstract protected function trigger_action( $request );
}
