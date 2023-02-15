<?php

namespace BigCommerce\Webhooks;

use BigCommerce\Settings\Sections\Import;
use BigCommerce\Webhooks\Customer\Customer_Channel_Webhook;
use BigCommerce\Webhooks\Customer\Customer_Create_Webhook;
use BigCommerce\Webhooks\Customer\Customer_Delete_Webhook;
use BigCommerce\Webhooks\Customer\Customer_Update_Webhook;

trait WebhookTrait {

	/**
	 * Is product webhooks are enabled
	 *
	 * @return bool
	 */
	public function product_webhooks_enabled(): bool {
		return (bool) get_option( Import::ENABLE_PRODUCTS_WEBHOOKS, 1 );
	}

	/**
	 * If customer webhooks are enabled
	 *
	 * @return bool
	 */
	public function customer_webhooks_enabled(): bool {
		return (bool) get_option( Import::ENABLE_CUSTOMER_WEBHOOKS, 1 );
	}

	/**
	 * Should we skip the product hook or no
	 *
	 * @param string $hook
	 * @param false  $hooks_enabled
	 *
	 * @return bool
	 */
	public function maybe_skip_product_hooks( $hook = '', $hooks_enabled = false ): bool {
		$is_customer_webhook = $this->is_customer_webhook( $hook );

		return ! $hooks_enabled && ! $is_customer_webhook;
	}

	/**
	 * Should we skip the customer hook or no
	 *
	 * @param string $hook
	 * @param false  $hooks_enabled
	 *
	 * @return bool
	 */
	public function maybe_skip_customer_hooks( $hook = '', $hooks_enabled = false ): bool {
		$is_customer_webhook = $this->is_customer_webhook( $hook );

		return ! $hooks_enabled && $is_customer_webhook;
	}

	/**
	 * Check if it is a customer webhook
	 *
	 * @param string $hook
	 *
	 * @return bool
	 */
	public function is_customer_webhook( $hook = '' ): bool {
		return in_array( $hook, $this->get_customer_webhooks() );
	}

	/**
	 * Return list of customer webhooks
	 *
	 * @return array
	 */
	public function get_customer_webhooks(): array {
		return [
			Customer_Create_Webhook::NAME,
			Customer_Update_Webhook::NAME,
			Customer_Delete_Webhook::NAME,
			Customer_Channel_Webhook::NAME,
		];
	}

}
