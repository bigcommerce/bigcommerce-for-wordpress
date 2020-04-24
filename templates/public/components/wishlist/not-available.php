<?php
/**
 * Component: Wish List Not Available
 *
 * @description Displays a message that the wish list is not available
 *
 * @var string  $message
 * @version 1.0.0
 */

if ( empty( $message ) ) {
	return;
}
?>

<h5 class="bc-wishlist__not-available"><?php echo $message; ?></h5>
