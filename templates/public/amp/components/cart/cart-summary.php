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
		<div class="bc-cart-subtotal">
			<span class="bc-cart-subtotal__label"><?php esc_html_e( 'Subtotal: ', 'bigcommerce' ); ?></span>
			<span class="bc-cart-subtotal__amount">{{ subtotal.formatted }}</span>
		</div>
		{{ #tax_amount }}
			<div class="bc-cart-tax">
				<span class="bc-cart-tax__label">
				{{ #tax_included }}
					<?php echo __( 'Estimated Tax Included in Subtotal: ', 'bigcommerce' ); ?>
				{{ /tax_included }}
				{{ ^tax_included }}
					<?php echo __( 'Estimated Taxes: ', 'bigcommerce' ); ?>
				{{ /tax_included }}
				</span>
				<span class="bc-cart-tax__amount">
					{{ tax_amount.formatted }}
				</span>
			</div>
		{{ /tax_amount }}
		<div class="bc-cart-total">
			<span class="bc-cart-total__label"><?php echo esc_html( __( 'Cart Total: ', 'bigcommerce' ) ); ?></span>
			<span class="bc-cart-total__amount">{{ total }}</span>
		</div>
	</template>
</amp-list>
