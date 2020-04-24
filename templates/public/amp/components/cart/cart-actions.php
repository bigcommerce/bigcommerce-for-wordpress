<?php
/**
 * Cart Actions
 *
 * @package BigCommerce
 *
 * @var string $checkout_url
 * @version 1.0.0
 */

use BigCommerce\Amp\Amp_Cart;
use BigCommerce\Container\Proxy;
?>

<div class="bc-cart-actions">
	<button
			[disabled]="savingItem"
			type="submit"
			on="tap:AMP.navigateTo(url='<?php echo esc_url( $checkout_url ); ?>')"
			class="bc-btn bc-cart-actions__checkout-button"
	><?php esc_html_e( 'Proceed to Checkout', 'bigcommerce' ); ?></button>
</div>
