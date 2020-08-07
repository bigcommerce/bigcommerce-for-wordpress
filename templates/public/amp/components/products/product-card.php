<?php
/**
 * @var Product $product
 * @var string  $title
 * @var string  $brand
 * @var string  $image
 * @var string  $price
 * @version 1.0.1
 */

use BigCommerce\Post_Types\Product\Product;

?>

<div class="bc-product-card" data-js="bc-product-loop-card">
	<?php echo wp_kses( $image, 'bigcommerce/amp' ); ?>

	<div class="bc-product__meta">
		<?php
		echo wp_kses( $title, 'bigcommerce/amp' );
		echo wp_kses( $brand, 'bigcommerce/amp' );
		echo wp_kses( $price, 'bigcommerce/amp' );
		?>
	</div>
	<?php if ( ! empty( $form ) ) { ?>
		<div class="bc-product__actions" data-js="bc-product-group-actions">
			<?php echo $form; // WPCS: XSS ok. Already escaped data. ?>
		</div>
	<?php } ?>
</div>
