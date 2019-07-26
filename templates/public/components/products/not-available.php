<?php
/**
 * Component: Product Not Available
 *
 * @description Displays a message that the product is not available
 *
 * @var string  $message
 */

if ( empty( $message ) ) {
	return;
}
?>

<h5 class="bc-product__not-available"><?php echo $message; ?></h5>
