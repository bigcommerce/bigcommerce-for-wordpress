<?php
/**
 * Container provider setting up Webhook classes
 *
 * @package BigCommerce
 */

namespace BigCommerce\Container;

use BigCommerce\Settings\Sections\Import as Import_Settings;
use BigCommerce\Webhooks\Checkout_Complete_Webhook;
use BigCommerce\Webhooks\Product_Inventory_Update_Webhook;
use BigCommerce\Webhooks\Product_Update_Webhook;
use BigCommerce\Webhooks\Product_Updater;
use BigCommerce\Webhooks\Status;
use BigCommerce\Webhooks\Webhook;
use BigCommerce\Webhooks\Webhook_Cron_Tasks;
use BigCommerce\Webhooks\Webhook_Listener;
use BigCommerce\Webhooks\Webhook_Versioning;
use Pimple\Container;

/**
 * Provider for webhooks
 */
class Webhooks extends Provider {
	const WEBHOOKS                         = 'webhooks.webhooks';
	const WEBHOOKS_STATUS                  = 'webhooks.webhooks_status';
	const WEBHOOKS_LISTENER                = 'webhooks.listener_webhook';
	const PRODUCT_UPDATE_WEBHOOK           = 'webhooks.product_update_webhook';
	const PRODUCT_INVENTORY_UPDATE_WEBHOOK = 'webhooks.inventory_update_webhook';
	const PRODUCT_UPDATER                  = 'webhooks.cron.product_updater';
	const CHECKOUT_COMPLETE_WEBHOOK        = 'webhooks.checkout_complete';
	const WEBHOOKS_VERSIONING              = 'webhooks.version';
	const WEBHOOKS_CRON_TASKS              = 'webhooks.cron_tasks';

	public function register( Container $container ) {
		$this->declare_webhooks( $container );
		$this->status( $container );
		$this->register_webhooks( $container );


		$this->handle_requests( $container );
		$this->webhook_actions( $container );
		$this->cron_actions( $container );
	}

	private function webhooks_enabled() {
		return get_option( Import_Settings::ENABLE_WEBHOOKS, 1 );
	}

	/**
	 * Declare all of the webhooks that will be registered by the plugin.
	 * The list of Webhook instances should be returned in $container[ self::WEBHOOKS ]
	 *
	 * @param Container $container
	 *
	 * @return void
	 */
	private function declare_webhooks( Container $container ) {
		$container[ self::WEBHOOKS ] = function ( Container $container ) {
			$webhooks = [
				$container[ self::PRODUCT_UPDATE_WEBHOOK ],
				$container[ self::PRODUCT_INVENTORY_UPDATE_WEBHOOK ],
				$container[ self::CHECKOUT_COMPLETE_WEBHOOK ],
			];

			/**
			 * Filter the webhooks that the plugin will register with BigCommerce
			 *
			 * @param Webhook[] $webhooks
			 */
			return apply_filters( 'bigcommerce/webhooks', $webhooks );
		};

		$container[ self::PRODUCT_UPDATE_WEBHOOK ] = function ( Container $container ) {
			return new Product_Update_Webhook( $container[ Api::FACTORY ]->webhooks() );
		};

		$container[ self::PRODUCT_INVENTORY_UPDATE_WEBHOOK ] = function ( Container $container ) {
			return new Product_Inventory_Update_Webhook( $container[ Api::FACTORY ]->webhooks() );
		};

		$container[ self::CHECKOUT_COMPLETE_WEBHOOK ] = function ( Container $container ) {
			return new Checkout_Complete_Webhook( $container[ Api::FACTORY ]->webhooks() );
		};
	}

	private function status( Container $container ) {
		$container[ self::WEBHOOKS_STATUS ] = function ( Container $container ) {
			return new Status( $container[ self::WEBHOOKS ], $container[ Api::FACTORY ]->webhooks() );
		};

		add_action( 'update_option_' . Import_Settings::ENABLE_WEBHOOKS, function( $old_value, $new_value, $option_name ) use ($container) {
			$container[ self::WEBHOOKS_STATUS ]->update_option( $old_value, $new_value, $option_name );
		}, 10, 3 );

		add_action( 'add_option_' . Import_Settings::ENABLE_WEBHOOKS, function( $option_name, $value ) use ($container) {
			$container[ self::WEBHOOKS_STATUS ]->update_option( null, $value, $option_name );
		}, 10, 2 );

		add_filter( 'bigcommerce/diagnostics', $this->create_callback( 'webhook_diagnostics', function ( $data ) use ( $container ) {
			return $container[ self::WEBHOOKS_STATUS ]->diagnostic_data( $data );
		} ), 10, 1 );
	}

	/**
	 * Register the webhooks with the BigCommerce API
	 *
	 * @param Container $container
	 *
	 * @return void
	 */
	private function register_webhooks( Container $container ) {
		$container[ self::WEBHOOKS_VERSIONING ] = function ( Container $container ) {
			return new Webhook_Versioning( $container[ self::WEBHOOKS ] );
		};

		add_action( 'bigcommerce/settings/webhoooks_updated', $this->create_callback( 'check_and_update_webhooks_version', function () use ( $container ) {
			$container[ self::WEBHOOKS_VERSIONING ]->maybe_update_webhooks();
		} ), 10, 0 );
	}

	/**
	 * Handle incoming requests for webhooks
	 *
	 * @param Container $container
	 *
	 * @return void
	 */
	private function handle_requests( Container $container ) {
		$container[ self::WEBHOOKS_LISTENER ] = function ( Container $container ) {
			return new Webhook_Listener( $container[ self::WEBHOOKS ] );
		};

		if ( ! $this->webhooks_enabled() ) {
			return;
		}

		// Listener for all webhook actions
		add_action( 'bigcommerce/action_endpoint/webhook', $this->create_callback( 'webhook_listener', function ( $args ) use ( $container ) {
			$container[ self::WEBHOOKS_LISTENER ]->handle_request( $args );
		} ), 10, 1 );
	}

	/**
	 * @param Container $container
	 */
	private function webhook_actions( Container $container ) {
		$container[ self::WEBHOOKS_CRON_TASKS ] = function ( Container $container ) {
			return new Webhook_Cron_Tasks();
		};

		if ( ! $this->webhooks_enabled() ) {
			return;
		}

		// Update product inventory webhook cron task
		add_action( 'bigcommerce/webhooks/product_inventory_updated', $this->create_callback( 'check_and_update_product_inventory_task', function ( $params ) use ( $container ) {
			$container[ self::WEBHOOKS_CRON_TASKS ]->set_product_update_cron_task( $params );
		} ), 10, 1 );

	}

	private function cron_actions( Container $container ) {
		$container[ self::PRODUCT_UPDATER ] = function ( Container $container ) {
			return new Product_Updater( $container[ Api::FACTORY ]->catalog(), $container[ Api::FACTORY ]->channels() );
		};

		if ( ! $this->webhooks_enabled() ) {
			return;
		}

		add_action( Webhook_Cron_Tasks::UPDATE_PRODUCT, $this->create_callback( 'update_product_cron_handler', function ( $product_id ) use ( $container ) {
			$container[ self::PRODUCT_UPDATER ]->update( $product_id );
		} ), 10, 1 );
	}
}
