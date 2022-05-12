<?php

namespace BigCommerce\Webhooks;

/**
 * Class Webhook_Listener
 *
 * @package BigCommerce\Webhooks
 */
class Webhook_Listener {

	use WebhookTrait;

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
	 * @param array $args
	 */
	public function handle_request( $args ): void {
		if ( ! array_key_exists( $args[0], $this->hooks ) ) {
			return;
		}

		$customer_webhooks_enabled = $this->customer_webhooks_enabled();
		$product_webhooks_enabled  = $this->product_webhooks_enabled();

		if ( $this->maybe_skip_product_hooks( $args[0], $product_webhooks_enabled ) || $this->maybe_skip_customer_hooks( $args[0], $customer_webhooks_enabled ) ) {
			return;
		}

		$this->hooks[ $args[0] ]->receive();
	}
}
