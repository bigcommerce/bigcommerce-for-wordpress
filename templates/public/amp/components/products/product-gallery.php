<?php
/**
 * Product Single Gallery
 *
 * @var Product $product
 * @var int[]   $image_ids
 * @var string  $fallback_image
 * @version 1.0.0
 */

use BigCommerce\Post_Types\Product\Product;
use BigCommerce\Assets\Theme\Image_Sizes;
?>

<div class="bc-product-gallery__images">

	<?php if ( $product->on_sale() ) { ?>
		<span class="bc-product-flag--sale"><?php esc_html_e( 'SALE', 'bigcommerce' ); ?></span>
	<?php } ?>

	<div class="bc-product-gallery--has-carousel">
		<amp-carousel id="bc-product-gallery-<?php echo esc_attr( $product->post_id() ); ?>"
					  class="bc-product-gallery__carousel"
					  width="370"
					  height="370"
					  layout="intrinsic"
					  type="slides">
			<?php
			if ( ! empty( $image_ids ) && is_array( $image_ids ) ) {
				foreach ( $image_ids as $image_id ) {
					$current_image       = wp_get_attachment_image_src( $image_id, Image_Sizes::BC_MEDIUM );
					$current_image_large = wp_get_attachment_image_src( $image_id, Image_Sizes::BC_LARGE );
					?>

					<amp-img src="<?php echo esc_url( $current_image[0] ); ?>"
							 width="<?php echo intval( $current_image[1] ); ?>"
							 height="<?php echo intval( $current_image[2] ); ?>"
							 srcset="<?php echo esc_url( $current_image[0] ); ?> <?php echo esc_attr( $current_image[1] ); ?>w, <?php echo esc_url( $current_image_large[0] ); ?> <?php echo esc_attr( $current_image_large[1] ); ?>w"
							 layout="intrinsic"
							 alt="<?php echo esc_attr( trim( strip_tags( get_post_meta( $image_id, '_wp_attachment_image_alt', true ) ) ) ); ?>"></amp-img>
					<?php
				}
			}
			?>
		</amp-carousel>
	</div>
	<div class="bc-product-gallery__thumbs">
		<?php
		if ( ! empty( $image_ids ) && is_array( $image_ids ) ) {
			foreach ( $image_ids as $index => $image_id ) {
				$current_image       = wp_get_attachment_image_src( $image_id, Image_Sizes::BC_THUMB );
				$current_image_large = wp_get_attachment_image_src( $image_id, Image_Sizes::BC_MEDIUM );
				?>
				<span on="tap:bc-product-gallery-<?php echo esc_attr( $product->post_id() ); ?>.goToSlide(index=<?php echo intval( $index ); ?>)" class="bc-product-gallery__thumb-slide" role="button" tabindex="0">
					<amp-img src="<?php echo esc_url( $current_image[0] ); ?>"
							 width="<?php echo intval( $current_image[1] ); ?>"
							 height="<?php echo intval( $current_image[2] ); ?>"
							 layout="intrinsic"
							 srcset="<?php echo esc_url( $current_image[0] ); ?> <?php echo esc_attr( $current_image[1] ); ?>w, <?php echo esc_url( $current_image_large[0] ); ?> <?php echo esc_attr( $current_image_large[1] ); ?>w"
							 class="bc-product-gallery__thumb-img"
							 alt="<?php echo esc_attr( trim( strip_tags( get_post_meta( $image_id, '_wp_attachment_image_alt', true ) ) ) ); ?>"></amp-img>
				</span>
				<?php
			}
		}
		?>
	</div>

</div>
