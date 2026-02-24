<?php
/**
 * Container provider setting up Webhook classes
 *
 * @package BigCommerce
 */

namespace BigCommerce\Container;

use BigCommerce\Settings\Sections\Import as Import_Settings;
use BigCommerce\Webhooks\Checkout_Complete_Webhook;
use BigCommerce\Webhooks\Customer\Customer_Channel_Updater;
use BigCommerce\Webhooks\Customer\Customer_Channel_Webhook;
use BigCommerce\Webhooks\Customer\Customer_Create_Webhook;
use BigCommerce\Webhooks\Customer\Customer_Creator;
use BigCommerce\Webhooks\Customer\Customer_Delete_Webhook;
use BigCommerce\Webhooks\Customer\Customer_Deleter;
use BigCommerce\Webhooks\Customer\Customer_Update_Webhook;
use BigCommerce\Webhooks\Customer\Customer_Updater;
use BigCommerce\Webhooks\Product\Channel_Updater;
use BigCommerce\Webhooks\Product\Channels_Assign;
use BigCommerce\Webhooks\Product\Channels_Currency_Update;
use BigCommerce\Webhooks\Product\Channels_Management_Webhook;
use BigCommerce\Webhooks\Product\Channels_UnAssign;
use BigCommerce\Webhooks\Product\Product_Create_Webhook;
use BigCommerce\Webhooks\Product\Product_Creator;
use BigCommerce\Webhooks\Product\Product_Delete_Webhook;
use BigCommerce\Webhooks\Product\Product_Inventory_Update_Webhook;
use BigCommerce\Webhooks\Product\Product_Update_Webhook;
use BigCommerce\Webhooks\Product\Product_Updater;
use BigCommerce\Webhooks\Status;
use BigCommerce\Webhooks\Webhook;
use BigCommerce\Webhooks\Webhook_Cron_Tasks;
use BigCommerce\Webhooks\Webhook_Listener;
use BigCommerce\Webhooks\Webhook_Versioning;
use Pimple\Container;

/**
 * Provider for handling BigCommerce webhooks.
 *
 * This class sets up webhook-related constants, services, and handlers 
 * to process webhook requests, manage their lifecycle, and integrate
 * with BigCommerce APIs.
 *
 * @package BigCommerce\Container
 */
class Webhooks extends Provider {

	/**
	 * Base identifier for webhooks.
	 * @var string
	 */
	const WEBHOOKS                         = 'webhooks.webhooks';

	/**
	 * Status of webhook enablement or disablement.
	 * @var string
	 */
	const WEBHOOKS_STATUS                  = 'webhooks.webhooks_status';

	/**
	 * Listener for handling incoming webhook payloads.
	 * @var string
	 */
	const WEBHOOKS_LISTENER                = 'webhooks.listener_webhook';

	/**
	 * General handler for channel webhooks.
	 * @var string
	 */
	const CHANNELS_HANDLE_WEBHOOK          = 'webhooks.channels_handle_webhook';

	/**
	 * Updates product information.
	 * @var string
	 */
	const PRODUCT_UPDATE_WEBHOOK           = 'webhooks.product_update_webhook';

	/**
	 * Deletes product data.
	 * @var string
	 */
	const PRODUCT_DELETE_WEBHOOK           = 'webhooks.product_delete_webhook';

	/**
	 * Creates new product entries.
	 * @var string
	 */
	const PRODUCT_CREATE_WEBHOOK           = 'webhooks.product_create_webhook';

	/**
	 * Updates product inventory.
	 * @var string
	 */
	const PRODUCT_INVENTORY_UPDATE_WEBHOOK = 'webhooks.inventory_update_webhook';

	/**
	 * Creates new customer entries.
	 * @var string
	 */
	const CUSTOMER_CREATE_WEBHOOK          = 'webhooks.customer_create_webhook';

	/**
	 * Updates customer details.
	 * @var string
	 */
	const CUSTOMER_UPDATE_WEBHOOK          = 'webhooks.customer_update_webhook';

	/**
	 * Deletes customer records.
	 * @var string
	 */
	const CUSTOMER_DELETE_WEBHOOK          = 'webhooks.customer_delete_webhook';

	/**
	 * Cron-based product updater.
	 * @var string
	 */
	const PRODUCT_UPDATER                  = 'webhooks.cron.product_updater';

	/**
	 * Cron-based product creator.
	 * @var string
	 */
	const PRODUCT_CREATOR                  = 'webhooks.cron.product_creator';

	/**
	 * Assigns a product to a channel.
	 * @var string
	 */
	const CHANNEL_PRODUCT_ASSIGNED         = 'webhooks.product.channels_assign';

	/**
	 * Unassigns a product from a channel.
	 * @var string
	 */
	const CHANNEL_PRODUCT_UNASSIGNED       = 'webhooks.product.channels_unassign';

	/**
	 * Updates channel details.
	 * @var string
	 */
	const CHANNEL_UPDATER                  = 'webhooks.product.channels_updater';

	/**
	 * Updates currency configuration for channels.
	 * @var string
	 */
	const CHANNEL_CURRENCY_UPDATED         = 'webhooks.channels.currency_updated';

	/**
	 * Cron-based customer creator.
	 * @var string
	 */
	const CUSTOMER_CREATOR                 = 'webhooks.cron.customer_creator';

	/**
	 * Cron-based customer updater.
	 * @var string
	 */
	const CUSTOMER_UPDATER                 = 'webhooks.cron.customer_updater';

	/**
	 * Cron-based customer deleter.
	 * @var string
	 */
	const CUSTOMER_DELETER                 = 'webhooks.cron.customer_deleter';

	/**
	 * Updates customer channel access.
	 * @var string
	 */
	const CUSTOMER_CHANNEL_ACCESS_UPDATER  = 'webhooks.cron.customer_channel_access_updater';

	/**
	 * Handles customer access per channel.
	 * @var string
	 */
	const CUSTOMER_CHANNEL_ACCESS          = 'webhooks.cron.customer_channel_access';

	/**
	 * Triggered when a checkout process is completed.
	 * @var string
	 */
	const CHECKOUT_COMPLETE_WEBHOOK        = 'webhooks.checkout_complete';

	/**
	 * Versioning logic for webhooks.
	 * @var string
	 */
	const WEBHOOKS_VERSIONING              = 'webhooks.version';

	/**
	 * Cron tasks associated with webhooks.
	 * @var string
	 */
	const WEBHOOKS_CRON_TASKS              = 'webhooks.cron_tasks';

	/**
	 * Registers services related to webhooks in the container.
	 *
	 * Sets up webhook declarations, their statuses, and actions 
	 * for processing and managing webhook events.
	 *
	 * @param Container $container The Pimple container instance.
	 *
	 * @return void
	 */
	public function register( Container $container ) {
		$this->declare_webhooks( $container );
		$this->status( $container );
		$this->register_webhooks( $container );


		$this->handle_requests( $container );
		$this->webhook_actions( $container );
		$this->customer_webhook_actions( $container );
		$this->cron_actions( $container );
	}

	private function product_webhooks_enabled() {
		return get_option( Import_Settings::ENABLE_PRODUCTS_WEBHOOKS, 1 );
	}

	private function customer_webhooks_enabled() {
		return get_option( Import_Settings::ENABLE_CUSTOMER_WEBHOOKS, 1 );
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
				$container[ self::PRODUCT_CREATE_WEBHOOK ],
				$container[ self::CHANNELS_HANDLE_WEBHOOK ],
				$container[ self::PRODUCT_UPDATE_WEBHOOK ],
				$container[ self::PRODUCT_DELETE_WEBHOOK ],
				$container[ self::CUSTOMER_CREATE_WEBHOOK ],
				$container[ self::CUSTOMER_UPDATE_WEBHOOK ],
				$container[ self::CUSTOMER_DELETE_WEBHOOK ],
				$container[ self::CUSTOMER_CHANNEL_ACCESS ],
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

		$this->declare_product_webhooks( $container );
		$this->declare_customer_webhooks( $container );

		$container[ self::CHECKOUT_COMPLETE_WEBHOOK ] = function ( Container $container ) {
			return new Checkout_Complete_Webhook( $container[ Api::FACTORY ]->webhooks() );
		};

        $container[ self::PRODUCT_CREATOR ] = function ( Container $container ) {
            return new Product_Creator( $container[ Api::FACTORY ]->catalog(), $container[ Api::FACTORY ]->channels() );
        };

		$container[ self::CHANNEL_PRODUCT_ASSIGNED ] = function ( Container $container ) {
			return new Channels_Assign( $container[ Api::FACTORY ]->catalog(), $container[ Api::FACTORY ]->channels() );
		};

		$container[ self::CHANNEL_PRODUCT_UNASSIGNED ] = function ( Container $container ) {
			return new Channels_UnAssign( $container[ Api::FACTORY ]->catalog(), $container[ Api::FACTORY ]->channels() );
		};

		$container[ self::CHANNEL_CURRENCY_UPDATED ] = function ( Container $container ) {
			return new Channels_Currency_Update( $container[ Api::FACTORY ]->currencies_v3() );
		};

		$container[ self::CHANNEL_UPDATER ] = function ( Container $container ) {
			return new Channel_Updater( $container[ Api::FACTORY ]->catalog(), $container[ Api::FACTORY ]->channels() );
		};

		$container[ self::CUSTOMER_CREATOR ] = function ( Container $container ) {
			return new Customer_Creator( $container[ Api::FACTORY ]->customer() );
		};

		$container[ self::CUSTOMER_UPDATER ] = function ( Container $container ) {
			return new Customer_Updater( $container[ Api::FACTORY ]->customer() );
		};

		$container[ self::CUSTOMER_DELETER ] = function ( Container $container ) {
			return new Customer_Deleter( $container[ Api::FACTORY ]->customer() );
		};

		$container[ self::CUSTOMER_CHANNEL_ACCESS_UPDATER ] = function ( Container $container ) {
			return new Customer_Channel_Updater( $container[ Api::FACTORY ]->customer() );
		};
	}

	/**
	 * Declare
	 *
	 * @param \Pimple\Container $container
	 */
	private function declare_customer_webhooks( Container $container ) {
		$container[ self::CUSTOMER_CREATE_WEBHOOK ] = function ( Container $container ) {
			return new Customer_Create_Webhook( $container[ Api::FACTORY ]->webhooks() );
		};

		$container[ self::CUSTOMER_DELETE_WEBHOOK ] = function ( Container $container ) {
			return new Customer_Delete_Webhook( $container[ Api::FACTORY ]->webhooks() );
		};

		$container[ self::CUSTOMER_UPDATE_WEBHOOK ] = function ( Container $container ) {
			return new Customer_Update_Webhook( $container[ Api::FACTORY ]->webhooks() );
		};

		$container[ self::CUSTOMER_CHANNEL_ACCESS ] = function ( Container $container ) {
			return new Customer_Channel_Webhook( $container[ Api::FACTORY ]->webhooks() );
		};
	}

	/**
	 * Declare product related webhooks
	 *
	 * @param \Pimple\Container $container
	 */
	private function declare_product_webhooks( Container $container ) {
		$container[ self::PRODUCT_CREATE_WEBHOOK ] = function ( Container $container ) {
			return new Product_Create_Webhook( $container[ Api::FACTORY ]->webhooks() );
		};

		$container[ self::CHANNELS_HANDLE_WEBHOOK ] = function ( Container $container ) {
			return new Channels_Management_Webhook( $container[ Api::FACTORY ]->webhooks() );
		};

		$container[ self::PRODUCT_UPDATE_WEBHOOK ] = function ( Container $container ) {
			return new Product_Update_Webhook( $container[ Api::FACTORY ]->webhooks() );
		};

		$container[ self::PRODUCT_DELETE_WEBHOOK ] = function ( Container $container ) {
			return new Product_Delete_Webhook( $container[ Api::FACTORY ]->webhooks() );
		};

		$container[ self::PRODUCT_INVENTORY_UPDATE_WEBHOOK ] = function ( Container $container ) {
			return new Product_Inventory_Update_Webhook( $container[ Api::FACTORY ]->webhooks() );
		};
	}

	private function status( Container $container ) {
		$container[ self::WEBHOOKS_STATUS ] = function ( Container $container ) {
			return new Status( $container[ self::WEBHOOKS ], $container[ Api::FACTORY ]->webhooks() );
		};

		add_action( 'update_option_' . Import_Settings::ENABLE_PRODUCTS_WEBHOOKS, function( $old_value, $new_value, $option_name ) use ($container) {
			$container[ self::WEBHOOKS_STATUS ]->update_option( $old_value, $new_value, $option_name );
		}, 10, 3 );

		add_action( 'add_option_' . Import_Settings::ENABLE_PRODUCTS_WEBHOOKS, function( $option_name, $value ) use ($container) {
			$container[ self::WEBHOOKS_STATUS ]->update_option( null, $value, $option_name );
		}, 10, 2 );

		add_filter( 'bigcommerce/diagnostics', $this->create_callback( 'webhook_diagnostics', function ( $data ) use ( $container ) {
			return $container[ self::WEBHOOKS_STATUS ]->diagnostic_data( $data );
		} ), 10, 1 );

		add_action( 'update_option_' . Import_Settings::ENABLE_CUSTOMER_WEBHOOKS, function( $old_value, $new_value, $option_name ) use ($container) {
			$container[ self::WEBHOOKS_STATUS ]->update_option( $old_value, $new_value, $option_name );
		}, 10, 3 );

		add_action( 'add_option_' . Import_Settings::ENABLE_CUSTOMER_WEBHOOKS, function( $option_name, $value ) use ($container) {
			$container[ self::WEBHOOKS_STATUS ]->update_option( null, $value, $option_name );
		}, 10, 2 );
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

		if ( ! $this->product_webhooks_enabled() && ! $this->customer_webhooks_enabled() ) {
			return;
		}

		add_action( 'bigcommerce/action_endpoint/webhook', $this->create_callback( 'webhook_listener', function ( $args ) use ( $container ) {
			$container[ self::WEBHOOKS_LISTENER ]->handle_request( $args );
		} ), 10, 1 );
	}

	private function customer_webhook_actions( Container $container ) {
		if ( ! $this->customer_webhooks_enabled() ) {
			return;
		}

		add_action('bigcommerce/webhooks/customer_deleted', $this->create_callback('delete_customer_webhook_handler', function ( $params ) use ( $container ) {
			if ( ! $this->customer_webhooks_enabled() ) {
				return;
			}

			$container[ self::CUSTOMER_DELETER ]->handle_request( $params );
		} ), 10, 1 );

		add_action('bigcommerce/webhooks/customer_created', $this->create_callback('create_customer_webhook_handler', function ( $customer_id ) use ( $container ) {
			if ( ! $this->customer_webhooks_enabled() ) {
				return;
			}

			$container[ self::CUSTOMER_CREATOR ]->handle_request( $customer_id );
		} ), 10, 1 );

		add_action('bigcommerce/webhooks/customer_updated', $this->create_callback('update_customer_webhook_handler', function ( $customer_id ) use ( $container ) {
			if ( ! $this->customer_webhooks_enabled() ) {
				return;
			}

			$container[ self::CUSTOMER_UPDATER ]->handle_request( $customer_id );
		} ), 10, 1 );

		add_action( Customer_Channel_Webhook::HOOK, $this->create_callback('update_customer_channel_access_webhook_handler', function ( $customer_id, $channels_id ) use ( $container ) {
			if ( ! $this->customer_webhooks_enabled() ) {
				return;
			}

			$container[ self::CUSTOMER_CHANNEL_ACCESS_UPDATER ]->handle_request( $customer_id, $channels_id );
		} ), 10, 1 );
	}

	/**
	 * @param Container $container
	 */
	private function webhook_actions( Container $container ) {
		$container[ self::WEBHOOKS_CRON_TASKS ] = function ( Container $container ) {
			return new Webhook_Cron_Tasks();
		};

		if ( ! $this->product_webhooks_enabled() ) {
			return;
		}

		add_action( 'bigcommerce/webhooks/product_inventory_updated', $this->create_callback( 'check_and_update_product_inventory_task', function ( $params ) use ( $container ) {
			if ( ! $this->product_webhooks_enabled() ) {
				return;
			}

			$container[ Api::CACHE_HANDLER ]->flush_product_catalog_object_cache( $params['product_id'] );
			$container[ self::PRODUCT_UPDATER ]->update( $params['product_id'] );
		} ), 10, 1 );

		add_action( 'bigcommerce/webhooks/product_updated', $this->create_callback( 'check_and_update_product_data_task', function ( $params ) use ( $container ) {
			if ( ! $this->product_webhooks_enabled() ) {
				return;
			}

			$container[ Api::CACHE_HANDLER ]->flush_product_catalog_object_cache( $params['product_id'] );
			$container[ self::WEBHOOKS_CRON_TASKS ]->set_product_update_cron_task( $params );
		} ), 10, 1 );

        add_action('bigcommerce/webhooks/product_deleted', $this->create_callback('delete_single_product_handler', function ( $params ) use ( $container ) {
            if ( ! $this->product_webhooks_enabled() ) {
                return;
            }

            $container[ self::PRODUCT_DELETE_WEBHOOK ]->delete_the_product( $params );
        } ), 10, 1 );

        add_action('bigcommerce/webhooks/product_created', $this->create_callback('create_single_product_handler', function ( $params ) use ( $container ) {
            if ( ! $this->product_webhooks_enabled() ) {
                return;
            }

            $container[self::PRODUCT_CREATOR]->create($params);
        } ), 10, 1 );

		add_action ( sprintf('%s_assigned',Channels_Management_Webhook::PRODUCT_CHANNEL_HOOK ), $this->create_callback( 'product_channel_was_assigned', function ( $product_id, $channel_id ) use ( $container ) {
			if ( ! $this->product_webhooks_enabled() ) {
				return;
			}

			$container[ self::CHANNEL_PRODUCT_ASSIGNED ]->handle_request( $product_id, $channel_id );
		} ), 10, 2 );

		add_action ( sprintf( '%s_unassigned', Channels_Management_Webhook::PRODUCT_CHANNEL_HOOK ), $this->create_callback( 'product_channel_was_unassigned', function ( $product_id, $channel_id ) use ( $container ) {
			if ( ! $this->product_webhooks_enabled() ) {
				return;
			}

			$container[ self::CHANNEL_PRODUCT_UNASSIGNED ]->handle_request( $product_id, $channel_id );
		} ), 10, 2 );

		add_action ( Channels_Management_Webhook::CHANNEL_CURRENCY_UPDATE_HOOK, $this->create_callback( 'channel_currency_was_updated', function ( $channel_id ) use ( $container ) {
			if ( ! $this->product_webhooks_enabled() ) {
				return;
			}

			$container[ self::CHANNEL_CURRENCY_UPDATED ]->handle_request( $channel_id );
		} ), 10, 2 );

		add_action ( Channels_Management_Webhook::CHANNEL_UPDATED_HOOK, $this->create_callback( 'bc_channel_was_updated', function ( $channel_id ) use ( $container ) {
			$container[ self::CHANNEL_UPDATER ]->handle_request( $channel_id );
		} ), 10, 2 );
	}

	private function cron_actions( Container $container ) {
		$container[ self::PRODUCT_UPDATER ] = function ( Container $container ) {
			return new Product_Updater( $container[ Api::FACTORY ]->catalog(), $container[ Api::FACTORY ]->channels() );
		};

		if ( ! $this->product_webhooks_enabled() ) {
			return;
		}

		add_action( Webhook_Cron_Tasks::UPDATE_PRODUCT, $this->create_callback( 'update_product_cron_handler', function ( $product_id ) use ( $container ) {
            $container[ self::PRODUCT_UPDATER ]->update( $product_id );
		} ), 10, 1 );
	}
}
