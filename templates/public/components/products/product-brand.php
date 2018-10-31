<?php
/**
 * Component: Product Brand
 *
 * @description Displays the brand of the product
 *
 * @var string  $brand
 * @var Product $product
 */

use BigCommerce\Post_Types\Product\Product;

if ( empty( $brand ) ) {
	return;
}
?>
<span class="bc-product__brand"><?php echo esc_html( $brand ); ?></span>