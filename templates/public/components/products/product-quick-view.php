<?php
/**
 * Product Quick View Card.
 *
 * @package BigCommerce
 *
 * @var Product $product
 * @var string  $sku
 * @var string  $rating
 * @var string  $gallery
 * @var string  $title
 * @var string  $brand
 * @var string  $price
 * @var string  $description
 * @var string  $specs
 * @var string  $form      The form to purchase the product
 * @var string  $permalink A button linking to the product
 */

use BigCommerce\Post_Types\Product\Product;

?>
<!-- data-js="bc-product-data-wrapper" is required. -->
<div id="bc-product-<?php echo esc_attr( $sku ); ?>--quick-view" class="bc-product-card bc-product-card--single" data-js="bc-product-data-wrapper">
	<?php echo $gallery; ?>

	<div class="bc-product__meta">
		<?php
		echo $title;
		echo $brand;
		echo $price;
		echo $rating;
		echo sprintf( __( '<span class="bc-product-single__meta-label">SKU:</span> %s', 'bigcommerce' ), $sku );
		?>

	</div>

	<div class="bc-product__actions">
		<?php echo $form; ?>
	</div>

	<?php echo $description; ?>
</div>
