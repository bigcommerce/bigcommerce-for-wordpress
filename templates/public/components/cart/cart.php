<?php
/**
 * Cart
 *
 * @package BigCommerce
 *
 * @var array  $cart
 * @var string $error_message The error message container
 * @var string $header        The cart table layout header
 * @var string $items         The cart items
 * @var string $footer        The cart table layout footer
 * @version 1.0.0
 */

?>
<!-- data-js="bc-cart" is required -->
<section class="bc-cart" data-js="bc-cart" data-cart_id="<?php echo esc_attr( $cart['cart_id'] ); ?>">
	<?php
	echo $error_message;
	echo $header;
	echo $items;
	echo $footer;
	?>
</section>
