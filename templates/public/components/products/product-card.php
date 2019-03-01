<?php
/**
 * Product Card used in loops and grids.
 *
 * @package BigCommerce
 * @since v1.7
 *
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

<!-- data-js="bc-product-quick-view-dialog-trigger" is required -->
<button type="button" class="bc-quickview-trigger"
        data-js="bc-product-quick-view-dialog-trigger"
        data-content=""
        data-productid="<?php echo $product->post_id(); ?>"
        <?php echo $attributes;?>
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
<!-- data-quick-view-script="" is required -->
<script data-quick-view-script="" type="text/template">
	<!-- data-js="bc-product-quick-view-content" is required -->
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
	<!-- data-js="bc-product-group-actions" is required -->
	<div class="bc-product__actions" data-js="bc-product-group-actions">
		<?php echo $form; ?>
	</div>
<?php } ?>
