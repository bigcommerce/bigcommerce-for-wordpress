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

<section [class]="!savingItem ? 'bc-cart' : 'bc-cart bc-updating-cart'" class="bc-cart">
	<?php
	echo $error_message;
	echo $header;
	echo $items;
	echo $footer;
	?>
</section>
