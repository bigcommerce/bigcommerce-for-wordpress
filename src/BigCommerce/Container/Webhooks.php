<?php
/**
 * Container provider setting up Webhook classes
 *
 * @package BigCommerce
 */

namespace BigCommerce\Container;

use BigCommerce\Webhooks\Product_Update_Webhook;
use Pimple\Container;

/**
 * Provider for webhooks
 */
class Webhooks extends Provider {
	const PRODUCT_UPDATE_WEBHOOK = 'webhooks.product_update_webhook';

	/**
	 * Sets up webhook classes.
	 *
	 * @param Container $container Container instance.
	 */
	public function register( Container $container ) {
		$this->product_update_webhook( $container );
	}

	/**
	 * Sets up the product update webhook
	 *
	 * @param Container $container The webhooks container.
	 */
	private function product_update_webhook( Container $container ) {
		$container[ self::PRODUCT_UPDATE_WEBHOOK ] = function ( Container $container ) {
			$product_update = new Product_Update_Webhook( $container[ Api::FACTORY ]->webhooks() );
			return $product_update;
		};

		add_action(
			'bigcommerce/settings/api_credentials_updated',
			$this->create_callback(
				'set_up_products_webhook',
				function() use ( $container ) {
					$container[ self::PRODUCT_UPDATE_WEBHOOK ]->update();
				}
			)
		);

		add_action(
			'bigcommerce/action_endpoint/' . Product_Update_Webhook::ACTION,
			$this->create_callback(
				'products_receive',
				function() use ( $container ) {
					$container[ self::PRODUCT_UPDATE_WEBHOOK ]->receive();
				}
			)
		);
	}
}
