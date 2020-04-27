<?php
/**
 * Mini-Cart Summary
 *
 * @package BigCommerce
 *
 * @var array $cart
 * @version 1.0.0
 */

?>
<!-- class="bc-cart-subtotal" is required -->
<div class="bc-cart-subtotal">
	<span class="bc-cart-subtotal__label"><?php esc_html_e( 'Subtotal: ', 'bigcommerce' ); ?></span>
	<!-- class="bc-cart-subtotal__amount" is required -->
	<span class="bc-cart-subtotal__amount"><?php echo esc_html( $cart['subtotal']['formatted'] ); ?></span>
</div>
