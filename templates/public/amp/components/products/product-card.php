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
			data-productid="<?php echo intval( $product->post_id(), 10 ); ?>"
		<?php echo $attributes; // WPCS: XSS ok. Already escaped data. ?>
			on="tap:bc-product-<?php echo intval( $product->post_id(), 10 ); ?>--quick-view"
	>
		<?php echo wp_kses( $image, 'bigcommerce/amp' ); ?>
		<?php if ( $quick_view ) { ?>
			<div class="bc-quickview-trigger--hover">
			<span class="bc-quickview-trigger--hover-label">
				<?php echo esc_html( __( 'Quick View', 'bigcommerce' ) ); ?>
			</span>
			</div>
		<?php } ?>
	</button>
	<?php if ( $quick_view ) { ?>
		<amp-lightbox id="bc-product-<?php echo intval( $product->post_id(), 10 ); ?>--quick-view"
					  layout="nodisplay"
					  scrollable>
			<div tabindex="-1" class="bc-product-quick-view__overlay"></div>
			<div class="bc-product-quick-view__content">
				<div role="document">
					<button type="button"
						 aria-label="<?php echo esc_attr( __( 'Close Quick View', 'bigcommerce' ) ); ?>"
						 class="bc-product-quick-view__close-button bc-icon icon-bc-cross"
						 tabindex="<?php echo intval( $product->post_id(), 10 ); ?>"
						 on="tap:bc-product-<?php echo intval( $product->post_id(), 10 ); ?>--quick-view.close">
						<span class="u-bc-visual-hide">
							<?php esc_html_e( 'Close Quick View', 'bigcommerce' ); ?>
						</span>
					</button>
					<div class="bc-product-quick-view__content-inner">
						<?php echo $quick_view; // WPCS: XSS ok. Already escaped data. ?>
					</div>
				</div>
			</div>
		</amp-lightbox>
	<?php } ?>

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
