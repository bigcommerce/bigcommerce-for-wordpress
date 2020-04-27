<?php

/**
 * Instructions for redeeming gift certificates. By default, these instructions will
 * be displayed underneath the forms to purchase a gift certificate or check its
 * balance.
 * 
 * @version 1.0.0
 */

?>
<aside class="bc-gift-instructions">
	<h3><?php echo esc_html( __( 'How to Redeem Gift Certificates', 'bigcommerce' ) ); ?></h3>
	<p><?php echo esc_html( __( 'To redeem a gift certificate, follow these steps:', 'bigcommerce' ) ); ?></p>
	<ol>
		<li><?php echo esc_html( __( 'You need your unique gift certificate code, which is part of the gift certificate that was emailed to you as an attachment. It will look something like Z50-Y6K-COS-402.', 'bigcommerce' ) ); ?></li>
		<li><?php echo esc_html( __( 'Browse the store and add items to your cart.', 'bigcommerce' ) ); ?></li>
		<li><?php echo esc_html( __( 'Visit the cart to view the items you have selected to purchase.', 'bigcommerce' ) ); ?></li>
		<li><?php echo esc_html( __( 'Click "Process to Checkout".', 'bigcommerce' ) ); ?></li>
		<li><?php echo esc_html( __( 'Type your gift certificate code into the "Promo/Gift Certificate" box and click "Apply" to apply the balance.', 'bigcommerce' ) ); ?></li>
	</ol>
</aside>
