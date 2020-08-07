<?php
/**
 * Product Card used in loops and grids.
 *
 * @package BigCommerce
 * @since v1.7
 *
 * @var Product $product
 * @var string  $title
 * @var string  $brand
 * @var string  $image
 * @var string  $price
 * @version 1.0.1
 */

use BigCommerce\Post_Types\Product\Product;

?>
<?php echo $image; ?>

<div class="bc-product__meta">
	<?php
	echo $title;
	echo $brand;
	echo $price;
	?>
</div>
<?php if ( ! empty( $form ) ) { ?>
	<!-- data-js="bc-product-group-actions" is required -->
	<div class="bc-product__actions" data-js="bc-product-group-actions">
		<?php echo $form; ?>
	</div>
<?php } ?>
