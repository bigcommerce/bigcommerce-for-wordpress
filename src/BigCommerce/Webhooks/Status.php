<?php

namespace BigCommerce\Webhooks;


use BigCommerce\Api\Webhooks_Api;

class Status {

	/**
	 * @var Webhook[]
	 */
	private $hooks;
	/**
	 * @var Webhooks_Api
	 */
	private $api;

	/**
	 * Status constructor.
	 *
	 * @param Webhook[]    $hooks
	 * @param Webhooks_Api $api
	 */
	public function __construct( array $hooks, Webhooks_Api $api ) {
		$this->api = $api;
		foreach ( $hooks as $hook ) {
			$this->hooks[ $hook->get_name() ] = $hook;
		}
	}

	/**
	 * @param array $data
	 *
	 * @return array
	 *
	 * @filter bigcommerce/diagnostics
	 */
	public function diagnostic_data( $data ) {
		$webhooks = $this->api->listWebhooks();

		// order by scope ASC, is_active DESC, destination ASC
		usort( $webhooks, function ( $a, $b ) {
			if ( $a->scope === $b->scope ) {
				if ( $a->is_active === $b->active ) {
					return strcmp( $a->destination, $b->destination );
				}

				return $a->is_active ? - 1 : 1;
			}

			return strcmp( $a->scope, $b->scope );
		} );

		$webhook_data = [];

		$scopes = array_unique( wp_list_pluck( $webhooks, 'scope' ) );
		foreach ( $scopes as $scope ) {
			$webhook_data[] = [
				'label' => $scope,
				'key'   => sprintf( 'webhook-%s', $scope ),
				'value' => '<ul>' . implode( array_map( function ( $hook ) {
						$disabled = $hook->is_active ? '' : ( ' ' . __( '(disabled)', 'bigcommerce' ) );

						return sprintf( '<li>%s%s</li>', esc_url( $hook->destination ), $disabled );
					}, array_filter( $webhooks, function ( $hook ) use ( $scope ) {
						return $hook->scope === $scope;
					} ) ) ) . '</ul>',
			];
		}

		$data [] = [
			'label' => __( 'Webhooks', 'bigcommerce' ),
			'key'   => 'webhooks',
			'value' => $webhook_data,
		];

		return $data;
	}

	/**
	 * @param $old_value
	 * @param $new_value
	 * @param $option_name
	 *
	 * @action update_option_bigcommerce_import_enable_webhooks
	 */
	public function update_option( $old_value, $new_value, $option_name ) {
		if ( $old_value === $new_value ) {
			return;
		}

		// If we are enabling webhooks, call the existing action.
		if ( $new_value ) {
			do_action( 'bigcommerce/settings/webhoooks_updated' );

			return;
		}

		$api_webhooks = (array) $this->api->listWebhooks();

		// Delete the hooks.
		foreach ( $this->hooks as $hook ) {
			$matching    = array_filter( $api_webhooks, function ( $api_hook ) use ( $hook ) {
				return $hook->scope() === $api_hook->scope && $hook->destination() === $api_hook->destination;
			} );
			foreach ( $matching as $api_hook ) {
				$hook->delete( $api_hook->id );
			}
		}

		// Reset the hooks options so we can re-enable.
		delete_option( 'schema-' . Webhook_Versioning::class );
		delete_option( Webhook::WEBHOOKS_OPTION );
		delete_option( Webhook::AUTH_KEY_OPTION );
	}

}
