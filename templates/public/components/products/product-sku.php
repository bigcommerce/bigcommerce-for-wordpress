<?php
/**
 * Component: Product SKU
 *
 * @description Displays the sku of the product
 *
 * @var string  $sku
 * @var Product $product
 */

use BigCommerce\Post_Types\Product\Product;

if ( empty( $sku ) ) {
	return;
}
?>

<span class="bc-product-single__meta-label">
	<?php esc_html_e( 'SKU:', 'bigcommerce' ); ?>
</span>

<?php echo esc_html( $sku ); ?>