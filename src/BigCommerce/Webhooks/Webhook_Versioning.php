<?php

namespace BigCommerce\Webhooks;


use BigCommerce\Settings\Sections\Import;
use BigCommerce\Webhooks\Customer\Customer_Create_Webhook;
use BigCommerce\Webhooks\Customer\Customer_Delete_Webhook;
use BigCommerce\Webhooks\Customer\Customer_Update_Webhook;

/**
 * Class Webhook_Versioning
 *
 * Handle webhook update logic depends on the webhooks version
 *
 * @package BigCommerce\Webhooks
 */
class Webhook_Versioning {
	const VERSION = 2;

	/**
	 * @var Webhook[]
	 */
	private $hooks;

	/**
	 * Webhook_Listener constructor.
	 *
	 * @param Webhook[] $hooks
	 */
	public function __construct( array $hooks ) {
		foreach ( $hooks as $hook ) {
			$this->hooks[ $hook->get_name() ] = $hook;
		}
	}

	/**
	 * @return void
	 * @action bigcommerce/import/fetched_store_settings
	 * @action bigcommerce/settings/webhoooks_updated
	 */
	public function maybe_update_webhooks() {
		if ( ! $this->product_webhooks_enabled() && ! $this->customer_webhooks_enabled() ) {
			return;
		}

		$version_option = 'schema-' . self::class;
		if ( (int) get_option( $version_option, 0 ) !== self::VERSION ) {
			$this->update_webhooks();
			update_option( 'schema-' . self::class, self::VERSION );
		}
	}

	private function product_webhooks_enabled() {
		return get_option( Import::ENABLE_PRODUCTS_WEBHOOKS , 1 );
	}

	private function customer_webhooks_enabled() {
		return get_option( Import::ENABLE_CUSTOMER_WEBHOOKS , 1 );
	}

	/**
	 * @return void Set new routes whenever any of the route list element gets updated
	 */
	private function update_webhooks() {
		$customer_webhooks         = [
			Customer_Create_Webhook::NAME,
			Customer_Update_Webhook::NAME,
			Customer_Delete_Webhook::NAME,
		];
		$customer_webhooks_enabled = $this->customer_webhooks_enabled();
		$product_webhooks_enabled  = $this->product_webhooks_enabled();

		foreach ( $this->hooks as $key => $hook ) {
			/**
			 * Check if only product or customer webhooks are enabled
			 * We don't need to update all webhooks
			 */
			$is_customer_webhook       = in_array( $key, $customer_webhooks );
			$should_skip_customer_hook = ! $customer_webhooks_enabled && $is_customer_webhook;
			$should_skip_product_hook  = ! $product_webhooks_enabled && ! $is_customer_webhook;

			if ( $should_skip_customer_hook || $should_skip_product_hook ) {
				continue;
			}

			$hook->update();
		}
	}

}
