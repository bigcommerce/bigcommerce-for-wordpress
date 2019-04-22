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
<!-- data-js="bc-cached-product-pricing" is required. -->
<p class="bc-product__pricing--cached bc-product__pricing--visible" data-js="bc-cached-product-pricing">
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
</p>

<!-- data-pricing-api-product-id & data-js="bc-api-product-pricing" is required. -->
<p class="bc-product__pricing--api" data-js="bc-api-product-pricing" data-pricing-api-product-id="<?php echo esc_attr( $product->bc_id() ); ?>">
	<!-- class="bc-product-price bc-product__price--base" is required -->
	<span class="bc-product-price bc-product__price--base"></span>
	<!-- class="bc-product__original-price" is required -->
	<span class="bc-product__original-price"></span>
	<!-- class="bc-product-price bc-product__price--sale" is required -->
	<span class="bc-product__price bc-product__price--sale"></span>
</p>
