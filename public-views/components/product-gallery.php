<?php
/**
 * Product Single Gallery
 *
 * @var Product $product
 * @var int[]   $image_ids
 * @var string  $fallback_image
 */

use BigCommerce\Post_Types\Product\Product;

$gallery_classes = count( $image_ids ) > 1 ? 'swiper-container bc-product-gallery--has-carousel' : 'swiper-container';
?>
<div class="bc-product__gallery">
	<div class="bc-product-gallery__images" data-js="bc-product-gallery">
		<?php if ( $product->on_sale() ) { ?>
			<span class="bc-product-flag--sale"><?php esc_html_e( 'SALE', 'bigcommerce' ); ?></span>
		<?php } ?>

		<div class="<?php echo esc_attr( $gallery_classes ); ?>" data-js="bc-gallery-container">
			<div class="swiper-wrapper">
				<?php if ( $image_ids ) {
					foreach ( $image_ids as $image_id ) { ?>
						<div class="swiper-slide bc-product-gallery__image-slide">
							<img src="<?php echo esc_url( wp_get_attachment_image_url( $image_id, 'bc-medium' ) ); ?>"
							     alt="<?php echo esc_attr( trim( strip_tags( get_post_meta( $image_id, '_wp_attachment_image_alt', true ) ) ) ); ?>">
						</div>
					<?php }
				} else { ?>
					<div class="swiper-slide bc-product-gallery__image-slide">
						<?php echo $fallback_image; ?>
					</div>
				<?php } ?>
			</div>
		</div>

		<?php if ( count( $image_ids ) > 1 ) { ?>
			<div class="swiper-container" data-js="bc-gallery-thumbs">
				<div class="swiper-wrapper bc-product-gallery__thumbs">
					<?php foreach ( $image_ids as $index => $image_id ) { ?>
						<a class="swiper-slide bc-product-gallery__thumb-slide" data-js="bc-gallery-thumb-trigger"
						   data-index="<?php echo (int) $index; ?>">
							<img src="<?php echo esc_url( wp_get_attachment_image_url( $image_id, 'bc-thumb' ) ); ?>"
							     alt="<?php echo esc_attr( trim( strip_tags( get_post_meta( $image_id, '_wp_attachment_image_alt', true ) ) ) ); ?>">
						</a>
					<?php } ?>
				</div>
			</div>
		<?php } ?>
	</div>
</div>