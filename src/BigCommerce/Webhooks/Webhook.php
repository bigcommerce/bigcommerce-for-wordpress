<?php
/**
 * Abstract class providing an interface for setting up BigCommerce webhooks.
 *
 * @package BigCommerce
 */

namespace BigCommerce\Webhooks;

/**
 * Sets up a webhook in the BigCommerce API to send event-based requests to the WP site.
 */
abstract class Webhook {
	const ACTION                = '';
	const SCOPE                 = '';
	const AUTH_HEADER           = 'X-WP-BigCommerce-Webhook-Auth-Header';
	const INPUT_AUTH_HEADER     = 'HTTP_X_WP_BIGCOMMERCE_WEBHOOK_AUTH_HEADER';
	const PASSWORD_ERROR_CODE   = 'bigcommerce_webhook_password_error';
	const VALIDATION_ERROR_CODE = 'bigcommerce_webhook_validation_error';

	/**
	 * Webhook constructor
	 *
	 * @param Webhooks_Api $api_client The client for making requests.
	 */
	public function __construct( $api_client ) {
		$this->api_client = $api_client;
	}

	/**
	 * Gets the key for saving the BigCommerce webhook ID to the WP database.
	 *
	 * @return string Option key.
	 */
	public function get_webhook_id_option_key() {
		return md5( 'bc_webhook_' . static::ACTION . static::SCOPE );
	}

	/**
	 * Gets the key for saving the webhook's auth password.
	 *
	 * @return string Option key.
	 */
	public function get_password_option_key() {
		return md5( 'bc_webhook_password_' . static::ACTION . static::SCOPE );
	}

	/**
	 * Returns the value of the auth header.
	 *
	 * @return string|false|null The value, or false or null if filter_input fails.
	 */
	public function get_auth_header() {
		return filter_input( INPUT_SERVER, self::INPUT_AUTH_HEADER );
	}

	/**
	 * Sends a request to BigCommerce to create a webhook.
	 *
	 * @param array $args Request arguments.
	 * @return array Webhook data or an error response on failure.
	 */
	public function create( $args ) {
		return $this->api_client->createWebhook( $args );
	}

	/**
	 * Sends a request to the BC API to update a webhook. Creates it if it doesn't exist.
	 *
	 * @param array $args Webhook arguments.
	 */
	public function update( $args = [] ) {
		$args = array_merge(
			[
				'scope'       => static::SCOPE,
				'destination' => sprintf( '%s/bigcommerce/%s', home_url(), static::ACTION ),
				'is_active'   => true,
			],
			$args
		);

		// Create a password for authenticating the incoming request from BigCommerce.
		$password = wp_generate_password( 32 );

		if ( empty( $password ) ) {
			return;
		}

		$password_option_key = $this->get_password_option_key();
		$args                = array_merge(
			$args,
			[ 'headers' => [ self::AUTH_HEADER => $password ] ]
		);

		$result = (array) $this->create( $args );

		if ( isset( $result['id'] ) ) {
			update_option( $password_option_key, $password );

			$webhook_id_option_key = $this->get_webhook_id_option_key();

			$previous_webhook_id = get_option( $webhook_id_option_key );
			if ( $previous_webhook_id && is_int( $previous_webhook_id ) ) {
				// Clean up obsolete web hook.
				$this->delete( $previous_webhook_id );
			}

			// Save the returned webhook ID as an option to help with cleanup later.
			update_option( $webhook_id_option_key, intval( $result['id'] ) );

			/**
			 * Fires when a webhook is added to the BigCommerce database.
			 *
			 * @param int Webhook ID.
			 * @param string Webhook action name.
			 * @param string Webhook scope.
			 */
			do_action(
				'bigcommerce/webhooks/webhook_updated',
				intval( $result['id'] ),
				static::ACTION,
				static::SCOPE
			);
		}
	}

	/**
	 * Deletes a webhook from the BigCommerce database.
	 *
	 * @param int $webhook_id The BC ID for the webhook entry.
	 */
	public function delete( $webhook_id ) {
		// deleteWebhook returns the deleted webhook on success.
		$deleted = $this->api_client->deleteWebhook( $webhook_id );

		if ( ! ! $deleted ) {

			/**
			 * Fires when a webhook is deleted from the BigCommerce database.
			 *
			 * @param int Webhook ID.
			 * @param string Webhook action name.
			 * @param string Webhook scope.
			 */
			do_action( 'bigcommerce/webhooks/webhook_deleted', $webhook_id, static::ACTION, static::SCOPE );
		}
	}


	/**
	 * Validates an incoming request.
	 *
	 * @param array  $request Request data.
	 * @param string $password The password to authenticate with.
	 * @return bool|\WP_Error True on validation or a WP_Error if the request isn't valid.
	 */
	public function validate( $request, $password = null ) {
		if ( ! $password ) {
			$password = $this->get_auth_header();
		}

		if ( ! $password || get_option( $this->get_password_option_key() ) !== $password ) {
			return new \WP_Error(
				static::PASSWORD_ERROR_CODE,
				__( 'Password header doesn\'t match.', 'bigcommerce' )
			);
		}

		if ( ! is_array( $request ) || ! isset( $request['data']['type'] ) || ! isset( $request['data']['id'] ) ) {
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
	abstract public function receive();
}
