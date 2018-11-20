<?php
/**
 * Empty Cart
 *
 * @package BigCommerce
 *
 */
?>

<section class="bc-cart" data-js="bc-cart" data-cart_id="<?php echo esc_attr( $cart['cart_id'] ); ?>">
	<div class="bc-cart-error">
		<!-- data-js="bc-cart-error-message" is required -->
		<p class="bc-cart-error__message" data-js="bc-cart-error-message"></p>
	</div>
	<header class="bc-cart-header">
		<div class="bc-cart-header__item"><?php esc_html_e( 'Item', 'bigcommerce' ); ?></div>
		<div class="bc-cart-header__qty"><?php esc_html_e( 'Qty', 'bigcommerce' ); ?></div>
		<div class="bc-cart-header__price"><?php esc_html_e( 'Price', 'bigcommerce' ); ?></div>
	</header>
	<div class="bc-cart__empty">
		<h2 class="bc-cart__title--empty"><?php _e( 'Your cart is empty.', 'bigcommerce' ); ?></h2>
		<a href="<?php echo esc_url( home_url() ); ?>" class="bc-cart__continue-shopping"><?php esc_html_e( 'Take a look around.', 'bigcpommerce' ); ?></a>
	</div>
</section>
