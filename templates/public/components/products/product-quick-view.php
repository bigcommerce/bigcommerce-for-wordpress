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
	echo $rating;
	echo $sku;
	?>

</div>

<div class="bc-product__actions">
	<?php echo $form; ?>
</div>

<?php echo $description; ?>
