<?php
/**
 * Component: Product Condition
 *
 * @description Displays the condition of the product
 *
 * @var string  $condition
 * @var Product $product
 */

use BigCommerce\Post_Types\Product\Product;
if ( empty( $condition ) ) {
	return;
}

?>
<span class="bc-product-flag--grey"><?php echo esc_html( $condition ); ?></span>
