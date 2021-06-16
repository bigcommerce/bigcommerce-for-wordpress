<?php
/**
 * Component: Product SKU
 *
 * @description Displays the sku of the product
 *
 * @var string  $sku
 * @var Product $product
 * @version 1.1.0
 */

use BigCommerce\Post_Types\Product\Product;

if ( empty( $sku ) ) {
	return;
}
?>

<span class="bc-product-single__meta-label">
	<?php esc_html_e( 'SKU:', 'bigcommerce' ); ?>
</span>

<!-- data-js="bc-product-sku" is required -->
<span class="bc-product-single__meta-sku" data-js="bc-product-sku">
	<?php echo esc_html( $sku ); ?>
</span>
