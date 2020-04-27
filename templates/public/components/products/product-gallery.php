<?php
/**
 * Product Single Gallery
 *
 * @package BigCommerce
 *
 * @var Product $product
 * @var int[]   $image_ids
 * @var array[] $youtube_videos
 * @var string  $fallback_image
 * @var string  $image_size     The image size to use for the gallery image
 * @var string  $thumbnail_size The image size to use for thumbnail images
 * @var string  $zoom_size      The image size to use for zoomed images
 * @var bool    $zoom           Whether image zoom is enabled
 * @version 1.0.0
 */

use BigCommerce\Post_Types\Product\Product;

$item_count = count( $image_ids ) + count( $youtube_videos );

$gallery_classes = $item_count > 1 ? 'swiper-container bc-product-gallery--has-carousel' : 'swiper-container';

$has_zoom = $zoom ? 'bc-product-image-zoom' : '';
?>

	<!-- data-js="bc-product-gallery" is required -->
	<div class="bc-product-gallery__images" data-js="bc-product-gallery">

		<?php if ( $product->on_sale() ) { ?>
			<span class="bc-product-flag--sale"><?php esc_html_e( 'SALE', 'bigcommerce' ); ?></span>
		<?php } ?>

		<!-- data-js="bc-gallery-container" is required -->
		<div class="<?php echo esc_attr( $gallery_classes ); ?>" data-js="bc-gallery-container">

			<!-- class="swiper-wrapper" is required -->
			<div class="swiper-wrapper" data-js="<?php esc_attr_e( $has_zoom ); ?>">
				<?php if ( $item_count > 0 ) {
					$index = 0;
					foreach ( $image_ids as $image_id ) {
						$image_src = wp_get_attachment_image_url( $image_id, $image_size );
						$image_full = $zoom ? sprintf( 'data-zoom="%s"', wp_get_attachment_image_url( $image_id, $zoom_size ) ) : '';
						$image_srcset = wp_get_attachment_image_srcset( $image_id, $image_size );
						?>
						<!-- class="swiper-slide" is required -->
						<div class="swiper-slide bc-product-gallery__image-slide" data-index="<?php echo $index++; ?>">
							<img
									src="<?php echo esc_url( $image_src ); ?>" <?php echo $image_full; ?>
									alt="<?php echo esc_attr( trim( strip_tags( get_post_meta( $image_id, '_wp_attachment_image_alt', true ) ) ) ); ?>"
									srcset="<?php echo esc_attr( $image_srcset ); ?>"
							>
						</div>
					<?php }
					foreach ( $youtube_videos as $video ) { ?>
						<!-- class="swiper-slide" is required -->
						<div
								class="swiper-slide bc-product-gallery__video-slide"
								data-js="bc-product-video-slide"
								data-index="<?php echo $index ++; ?>"
						>
							<?php echo $video['embed_html']; ?>
						</div>
					<?php }
				} else { ?>
					<div class="swiper-slide bc-product-gallery__image-slide">
						<?php echo $fallback_image; ?>
					</div>
				<?php } ?>
			</div>
		</div>

		<?php if ( $item_count > 1 ) { ?>
			<div class="swiper-container" data-js="bc-gallery-thumbs">

				<!-- class="swiper-wrapper" is required -->
				<div class="swiper-wrapper bc-product-gallery__thumbs">
					<?php
					$index = 0;
					foreach ( $image_ids as $image_id ) { ?>
						<!-- class="swiper-slide" and data-js="bc-gallery-thumb-trigger" are required -->
						<a class="swiper-slide bc-product-gallery__thumb-slide" data-js="bc-gallery-thumb-trigger"
						   data-index="<?php echo $index++; ?>">
							<img src="<?php echo esc_url( wp_get_attachment_image_url( $image_id, $thumbnail_size ) ); ?>"
							     alt="<?php echo esc_attr( trim( strip_tags( get_post_meta( $image_id, '_wp_attachment_image_alt', true ) ) ) ); ?>">
						</a>
					<?php }
					foreach ( $youtube_videos as $video ) { ?>
						<!-- class="swiper-slide bc-product-gallery__thumb-slide--video", data-player-id="<?php esc_attr( $video['id'] ); ?>", and data-js="bc-gallery-thumb-trigger" are required -->
						<a
								class="swiper-slide bc-product-gallery__thumb-slide bc-product-gallery__thumb-slide--video"
								data-js="bc-gallery-thumb-trigger"
								data-index="<?php echo $index++; ?>"
								data-player-id="<?php echo esc_attr( $video['id'] ); ?>"
								title="<?php echo esc_attr( sprintf( __( 'Play %s', 'bigcommerce' ), $video['title'] ) ); ?>"
						>
							<i class="bc-video-play-icon"></i>
						</a>
					<?php } ?>
				</div>
			</div>
		<?php } ?>

		<!-- If you've made changes to the gallery slide markup above, you should change it to match here as well. -->
		<!-- data-js="bc-product-variant-image" is required and class="swiper-slide" -->
			<div class="swiper-slide bc-product-gallery__image-slide bc-product-variant-image" data-js="bc-product-variant-image">
				<!-- data-js="bc-variant-image" is required -->
				<img src="" alt="" class="bc-variant-image" data-js="bc-variant-image">
			</div>

	</div>
