<?php
/**
 * Checkout_Complete_Webhook class
 *
 * @package BigCommmerce
 */

namespace BigCommerce\Webhooks;

/**
 * Sets up the webhook that runs when checkout completes
 */
class Checkout_Complete_Webhook extends Webhook {
	const SCOPE = 'store/cart/converted';
	const NAME  = 'checkout_complete';

	/**
	 * @param array $request
	 *
	 * @return void
	 */
	public function trigger_action( $request ) {
		$cart_id = $request['data']['id'];
		/**
		 * Fires when a product inventory webhooks has been received from the BigCommerce store.
		 *
		 * @param int $product_id BigCommerce product ID.
		 */
		do_action( 'bigcommerce/webhooks/' . self::NAME, [ 'cart_id' => $cart_id ] );
	}
}
