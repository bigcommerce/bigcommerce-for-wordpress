<?php
/**
 * Cart Summary
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

<?php if ( $cart['tax_amount']['raw'] > 0 ) { ?>
	<!-- class="bc-cart-tax" is required -->
	<div class="bc-cart-tax">
		<span class="bc-cart-tax__label"><?php echo esc_html( $cart['tax_included'] ? __( 'Tax Included in Subtotal: ', 'bigcommerce' ) : __( 'Tax: ', 'bigcommerce' ) ); ?></span>
		<!-- class="bc-cart-tax__amount" is required -->
		<span class="bc-cart-tax__amount"><?php echo esc_html( $cart['tax_amount']['formatted'] ); ?></span>
	</div>
<?php } ?>
