<?php
/**
 * Component: Product Hidden Price
 *
 * @since       3.2.0
 * @description Display hidden price message for the product
 *
 * @var Product $product
 * @var string  $message
 * @version 1.0.0
 */

use BigCommerce\Post_Types\Product\Product;

?>
<p class="bc-product__pricing--cached bc-product__pricing--visible">
	<span class="bc-product__price"><?php echo wp_strip_all_tags( $message ); ?></span>
</p>

