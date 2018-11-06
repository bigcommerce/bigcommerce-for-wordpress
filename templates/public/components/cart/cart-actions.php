<?php
/**
 * Cart Actions
 *
 * @package BigCommerce
 *
 * @var array $cart
 */

?>

<div class="bc-cart-actions">
	<form
			action="<?php echo esc_url( home_url( '/bigcommerce/checkout/' . $cart['cart_id'] ) ); ?>"
			method="post"
			enctype="multipart/form-data"
	>
		<button
				type="submit"
				class="bc-btn bc-cart-actions__checkout-button"
				data-js="proceed-to-checkout"
		><?php esc_html_e( 'Proceed to Checkout', 'bigcommerce' ); ?></button>
	</form>
	<?php do_action( 'bigcommerce/cart/proceed_to_checkout', $cart ); ?>
</div>
