<?php
/**
 * Product Card used in loops and grids.
 *
 * @package BigCommerce
 * @since v1.7
 *
 * @var Product $product
 * @var string  $image
 * @var string  $quick_view
 * @var string  $attributes
 * @version 1.0.0
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
	<div class="bc-quickview-trigger--hover">
		<span class="bc-quickview-trigger--hover-label">
			<?php echo esc_html( __( 'Quick View', 'bigcommerce' ) ); ?>
		</span>
	</div>
</button>
<!-- data-quick-view-script="" is required -->
<script data-quick-view-script="" type="text/template">
	<!-- data-js="bc-product-quick-view-content" is required -->
	<section class="bc-product-quick-view__content-inner" data-js="bc-product-quick-view-content">
		<?php echo $quick_view; ?>
	</section>
</script>

