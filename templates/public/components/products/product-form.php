<?php
/**
 * Product Single Form Actions
 *
 * @package BigCommerce
 *
 * @var Product $product
 * @var string  $options
 * @var string  $button
 * @var string  $message
 * @var int     $min_quantity
 * @var int     $max_quantity
 * @var bool    $ajax_add_to_cart
 * @var string  $quantity_field_type
 * @version 1.0.1
 */

use BigCommerce\Post_Types\Product\Product;

?>

<form action="<?php echo esc_url( $product->purchase_url() ); ?>" method="post" enctype="multipart/form-data"
      class="bc-form bc-product-form">
	<?php echo $options; ?>

	<!-- data-js="bc-product-message" is required. -->
	<div class="bc-product-form__product-message" data-js="bc-product-message"></div>

	<!-- data-js="variant_id" is required. -->
	<input type="hidden" name="variant_id" class="variant_id" data-js="variant_id" value="">

	<div class="bc-product-form__quantity">
		<?php if ( $quantity_field_type !== 'hidden' ) { ?>
		<label class="bc-product-form__quantity-label">
			<span class="bc-product-single__meta-label"><?php esc_html_e( 'Quantity', 'bigcommerce' ); ?>:</span>
		</label>
		<?php } ?>
		<input class="bc-product-form__quantity-input"
			type="<?php echo esc_attr( $quantity_field_type ); ?>"
			name="quantity"
			value="<?php echo absint( $min_quantity ); ?>"
			min="<?php echo absint( $min_quantity ); ?>"
			<?php if ( $max_quantity > 0 ) { ?>max="<?php echo absint( $max_quantity ); ?>"<?php } ?>
		/>
	</div>

	<?php if ( $message ) { ?>
		<span class="bc-product-form__purchase-message"><?php echo wp_strip_all_tags( $message ); ?></span>
	<?php } ?>

	<?php echo $button; ?>
	<?php if ( $ajax_add_to_cart ) { ?>
		<!-- data-js="bc-ajax-add-to-cart-message" is required. -->
		<div class="bc-ajax-add-to-cart__message-wrapper" data-js="bc-ajax-add-to-cart-message"></div>
	<?php } ?>
</form>
