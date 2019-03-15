<?php
/**
 * Container provider setting up Webhook classes
 *
 * @package BigCommerce
 */

namespace BigCommerce\Container;

use BigCommerce\Webhooks\Product_Inventory_Update_Webhook;
use BigCommerce\Webhooks\Product_Update_Webhook;
use BigCommerce\Webhooks\Product_Updater;
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
	const WEBHOOKS_LISTENER                = 'webhooks.listener_webhook';
	const PRODUCT_UPDATE_WEBHOOK           = 'webhooks.product_update_webhook';
	const PRODUCT_INVENTORY_UPDATE_WEBHOOK = 'webhooks.inventory_update_webhook';
	const PRODUCT_UPDATER                  = 'webhooks.cron.product_updater';
	const WEBHOOKS_VERSIONING              = 'webhooks.version';
	const WEBHOOKS_CRON_TASKS              = 'webhooks.cron_tasks';

	public function register( Container $container ) {
		$this->declare_webhooks( $container );
		$this->register_webhooks( $container );
		$this->handle_requests( $container );
		$this->webhook_actions( $container );
		$this->cron_actions( $container );
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

		add_action( 'bigcommerce/import/fetched_store_settings', $this->create_callback( 'check_and_update_webhooks_version', function () use ( $container ) {
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

		// Update product inventory webhook cron task
		add_action( 'bigcommerce/webhooks/product_inventory_updated', $this->create_callback( 'check_and_update_product_inventory_task', function ( $product_id ) use ( $container ) {
			$container[ self::WEBHOOKS_CRON_TASKS ]->set_product_update_cron_task( $product_id );
		} ), 10, 1 );

	}

	private function cron_actions( Container $container ) {
		$container[ self::PRODUCT_UPDATER ] = function ( Container $container ) {
			return new Product_Updater( $container[ Api::FACTORY ]->catalog(), $container[ Api::FACTORY ]->channels() );
		};

		add_action( Webhook_Cron_Tasks::UPDATE_PRODUCT, $this->create_callback( 'update_product_cron_handler', function ( $params ) use ( $container ) {
			$container[ self::PRODUCT_UPDATER ]->update( $params );
		} ), 10, 1 );
	}
}
