<?php
/**
 * @var Product $product
 * @var string  $title
 * @var string  $brand
 * @var string  $image
 * @var string  $price
 * @version 1.0.0
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
	<div class="bc-product__actions" data-js="bc-product-group-actions">
		<?php echo $form; ?>
	</div>
<?php } ?>
