<?php
/**
 * Product Single Form Actions
 *
 * @package BigCommerce
 *
 * @var Product $product
 * @var string $options
 * @var string $button
 */

use BigCommerce\Post_Types\Product\Product;
?>

<form action="<?php echo esc_url( $product->purchase_url() ); ?>" method="post" enctype="multipart/form-data"
      class="bc-form bc-product-form">
	<?php echo $options; ?>
	<div class="bc-product-form__product-message" data-js="bc-product-message"></div>
	<input type="hidden" name="variant_id" class="variant_id" data-js="variant_id" value="">
	<?php echo $button; ?>
</form>
