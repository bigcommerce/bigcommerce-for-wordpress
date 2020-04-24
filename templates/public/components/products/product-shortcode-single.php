<?php
/**
 * Single Product Card.
 *
 * @package BigCommerce
 *
 * @var Product $product
 * @var string  $gallery
 * @var string  $title
 * @var string  $brand
 * @var string  $price
 * @var string  $description
 * @var string  $form
 * @version 1.0.0
 */

use BigCommerce\Post_Types\Product\Product;

?>


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
