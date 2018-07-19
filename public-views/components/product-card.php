<?php
/**
 * @var Product $product
 * @var string  $title
 * @var string  $brand
 * @var string  $image
 * @var string  $price
 * @var string  $quick_view
 * @var string  $attributes
 */

use BigCommerce\Post_Types\Product\Product;

?>

<div class="bc-product-card" data-js="bc-product-loop-card">
	<button type="button" class="bc-quickview-trigger"
	        data-js="bc-product-quick-view-dialog-trigger"
	        data-content=""
	        data-productid="<?php echo $product->post_id(); ?>"
	        <?php echo esc_attr( $attributes );?>
	>
		<?php echo $image; ?>
		<?php if ( $quick_view ) { ?>
		<div class="bc-quickview-trigger--hover">
			<span class="bc-quickview-trigger--hover-label">
				<?php echo esc_html( __( 'Quick View', 'bigcommerce' ) ); ?>
			</span>
		</div>
		<?php } ?>
	</button>
	<?php if ( $quick_view ) { ?>
	<script data-js="" type="text/template">
		<section class="bc-product-quick-view__content-inner" data-js="bc-product-quick-view-content">
			<?php echo $quick_view; ?>
		</section>
	</script>
	<?php } ?>

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
</div>