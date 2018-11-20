<?php
/**
 * Product Single Form Actions
 *
 * @package BigCommerce
 *
 * @var Product $product
 * @var string  $options
 * @var string  $modifiers
 * @var string  $button
 * @var int     $min_quantity
 * @var int     $max_quantity
 */

use BigCommerce\Post_Types\Product\Product;

?>

<form action="<?php echo esc_url( $product->purchase_url() ); ?>" method="post" enctype="multipart/form-data"
      class="bc-form bc-product-form">
	<?php echo $options; ?>
	<?php echo $modifiers; ?>

	<!-- data-js="bc-product-message" is required. -->
	<div class="bc-product-form__product-message" data-js="bc-product-message"></div>

	<!-- data-js="variant_id" is required. -->
	<input type="hidden" name="variant_id" class="variant_id" data-js="variant_id" value="">
	<?php if ( $max_quantity != 1 ) { ?>
		<div class="bc-product-form__quantity">
			<label class="bc-product-form__quantity-label">
				<span class="bc-product-single__meta-label"><?php esc_html_e( 'Quantity', 'bigcommerce' ); ?>:</span>
			</label>
			<input class="bc-product-form__quantity-input"
			       type="number"
			       name="quantity"
			       value="<?php echo absint( $min_quantity ); ?>"
			       min="<?php echo absint( $min_quantity ); ?>"
			       <?php if ( $max_quantity > 0 ) { ?>max="<?php echo absint( $max_quantity ); ?>"<?php } ?>
			/>
		</div>
	<?php } ?>
	<?php echo $button; ?>
</form>
