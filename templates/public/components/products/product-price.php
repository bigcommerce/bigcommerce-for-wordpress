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

<?php if ( $product->on_sale() ) { ?>
	<!-- class="bc-product__original-price" is required. -->
	<span class="bc-product__original-price"><?php echo esc_html( $product->price_range() ) ?></span>
	<!-- class="bc-product__price" is required. -->
	<span class="bc-product__price bc-product__price--sale">
		<?php echo esc_html( $product->calculated_price_range() ); ?>
	</span>
<?php } else { ?>
	<!-- class="bc-product__price" is required. -->
	<span class="bc-product__price"><?php echo esc_html( $product->calculated_price_range() ); ?></span>
<?php } ?>
