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

$variant_id_expression = sprintf(
	'variants%1$d.allVariants.filter( a => a.options.filter( b => ( keys( variants%1$d.currentOptions ).filter( key => variants%1$d.currentOptions[ key ] == b.id && key == b.option_id ? true : false ) ).length ? true : false ).length == a.options.length ? a : false )[0].variant_id',
	intval( $product->post_id(), 10 )
);

?>

<form action-xhr="<?php echo esc_url( trailingslashit( $product->purchase_url() ) ); ?>" method="post" enctype="multipart/form-data" class="bc-form bc-product-form" target="_top">
	<?php echo $options; // WPCS: XSS ok. Already escaped data. ?>
	<?php echo $modifiers; // WPCS: XSS ok. Already escaped data. ?>
	<div class="bc-product-form__product-message" data-js="bc-product-message"></div>
	<input type="hidden" name="variant_id" class="variant_id" [value]="<?php echo esc_attr( $variant_id_expression ); ?>">
	<?php if ( 1 !== $max_quantity ) { ?>
		<div class="bc-product-form__quantity">
			<label class="bc-product-form__quantity-label">
				<span class="bc-product-single__meta-label"><?php esc_html_e( 'Quantity', 'bigcommerce' ); ?>:</span>
			</label>
			<input class="bc-product-form__quantity-input"
				type="number"
				name="quantity"
				value="<?php echo absint( $min_quantity ); ?>"
				min="<?php echo absint( $min_quantity ); ?>"
				<?php if ( $max_quantity > 0 ) : ?>
				max="<?php echo absint( $max_quantity ); ?>"
				<?php endif; ?>
				/>
		</div>
	<?php } ?>
	<?php echo $button; // WPCS: XSS ok. Already escaped data. ?>
</form>
