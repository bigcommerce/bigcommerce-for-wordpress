<?php
/**
 * Single Product Card.
 *
 * @package BigCommerce
 *
 * @var Product $product
 * @var string  $sku
 * @var string  $gallery
 * @var string  $title
 * @var string  $brand
 * @var string  $price
 * @var string  $description
 * @var string  $form
 */

use BigCommerce\Post_Types\Product\Product;

?>

<div id="bc-product-<?php echo esc_attr( $sku ); ?>" class="bc-product-card bc-product-card--single">
	<?php echo $gallery; ?>

	<div class="bc-product__meta">
		<?php

		echo $title;
		echo $brand;
		echo $price;
		echo $description;
		?>

	</div>

	<div class="bc-product__actions">
		<?php echo $form; ?>
	</div>
</div>
