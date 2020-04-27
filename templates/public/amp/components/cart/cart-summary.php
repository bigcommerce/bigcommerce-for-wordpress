<?php
/**
 * Cart Summary
 *
 * @package BigCommerce
 *
 * @var string $proxy_base
 * @version 1.0.0
 */

use BigCommerce\Cart\Cart;

?>

<amp-list
	layout="fixed-height"
	height="50"
	id="subtotal"
	src="<?php echo esc_url( rest_url( sprintf( '/%s/amp-cart?cart_id=CLIENT_ID(%s)', $proxy_base, Cart::CART_COOKIE ) ) ); ?>"
	single-item
	items="."
	class="bc-cart-subtotal"
	reset-on-refresh
	>
	<template type="amp-mustache">
		<span class="bc-cart-subtotal__label"><?php esc_html_e( 'Subtotal: ', 'bigcommerce' ); ?></span>
		<span class="bc-cart-subtotal__amount">{{ total }}</span>
	</template>
</amp-list>
