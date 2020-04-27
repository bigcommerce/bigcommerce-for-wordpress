<?php
/**
 * @var Product $product
 * @var int     $attachment_id
 * @var string  $size
 * @var string  $image
 * @version 1.0.0
 */

use BigCommerce\Post_Types\Product\Product;

?>

<div class="bc-product-card__featured-image">
	<?php if ( $product->on_sale() ) { ?>
		<span class="bc-product-flag--sale"><?php esc_html_e( 'SALE', 'bigcommerce' ); ?></span>
	<?php } ?>

	<?php
	if ( ! empty( $image_src ) ) {
		printf( '<amp-img src="%s" width="%s" height="%d" layout="intrinsic" alt="%s" srcset="%s"></amp-img>',
			esc_url( $image_src[0] ),
			intval( $image_src[1] ),
			intval( $image_src[2] ),
			esc_attr( trim( strip_tags( get_post_meta( $attachment_id, '_wp_attachment_image_alt', true ) ) ) ),
			isset( $image_srcset ) ? esc_attr( implode( ', ', $image_srcset ) ) : ''
		);
	}
	?>
</div>
