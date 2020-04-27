<?php
/**
 * @var Product $product
 * @var string  $images
 * @var string  $title
 * @var string  $brand
 * @var string  $price
 * @var string  $rating
 * @var string  $form
 * @var string  $description
 * @var string  $sku
 * @var string  $specs
 * @var string  $related
 * @var string  $reviews
 * @version 1.0.0
 */

use BigCommerce\Post_Types\Product\Product;

?>

<!-- data-js="bc-product-data-wrapper" is required. -->
<section class="bc-product-single__top" data-js="bc-product-data-wrapper">
	<?php echo $images; ?>

	<!-- data-js="bc-product-meta" is required. -->
	<div class="bc-product-single__meta" data-js="bc-product-meta">
		<?php echo $title; ?>
		<?php echo $brand; ?>
		<?php echo $price; ?>
		<?php echo $rating; ?>
		<?php echo $sku; ?>
		<?php echo $form; ?>
	</div>
</section>

<section class="bc-single-product__description">
	<h4 class="bc-single-product__section-title"><?php echo esc_html__( 'Product Description', 'bigcommerce' ); ?></h4>
	<?php echo $description; ?>
</section>

<?php echo $specs; ?>

<?php echo $reviews; ?>

<?php echo $related; ?>
