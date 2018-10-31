<?php
/**
 * Cart Summary
 *
 * @package BigCommerce
 *
 * @var array $cart
 */

?>

<div class="bc-cart-subtotal">
	<span class="bc-cart-subtotal__label"><?php esc_html_e( 'Subtotal: ', 'bigcommerce' ); ?></span>
	<span class="bc-cart-subtotal__amount"><?php echo esc_html( $cart['subtotal']['formatted'] ); ?></span>
</div>

<?php if ( $cart['tax_amount']['raw'] > 0 ) { ?>
	<div class="bc-cart-tax">
		<span class="bc-cart-tax__label"><?php echo esc_html( $cart['tax_included'] ? __( 'Tax Included in Subtotal: ', 'bigcommerce' ) : __( 'Tax: ', 'bigcommerce' ) ); ?></span>
		<span class="bc-cart-tax__amount"><?php echo esc_html( $cart['tax_amount']['formatted'] ); ?></span>
	</div>
<?php } ?>
