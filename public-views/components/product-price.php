<?php
/**
 * Component: Product Price
 *
 * @description Display the price for a product
 *
 * @var Product $product
 */

use BigCommerce\Post_Types\Product\Product;
?>

<p class="bc-product__pricing">
	<?php if ( $product->on_sale() ) { ?>
		<span class="bc-product__original-price"><?php echo esc_html( $product->price_range() ) ?></span>
		<span
			class="bc-product__price bc-product__price--sale"><?php echo esc_html( $product->calculated_price_range() ); ?></span>
	<?php } else { ?>
		<span class="bc-product__price"><?php echo esc_html( $product->calculated_price_range() ); ?></span>
	<?php } ?>
</p>
