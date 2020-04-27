<?php
/**
 * @var float   $stars        The number of stars out of 5
 * @var int     $percentage   The star rating converted to a percentage (e.g., 4.2 stars = 84%)
 * @var int     $review_count The number of reviews the product has received
 * @var string  $link         Destination for the link to reviews
 * @var Product $product
 * @version 1.0.0
 */

use BigCommerce\Post_Types\Product\Product;

?>
<div class="bc-single-product__ratings">
	<div class="bc-single-product__rating">
		<div class="bc-single-product__rating--mask" style="width: <?php echo intval( $percentage, 10 ); ?>%">
			<div class="bc-single-product__rating--top">
				<span class="bc-rating-star"></span>
				<span class="bc-rating-star"></span>
				<span class="bc-rating-star"></span>
				<span class="bc-rating-star"></span>
				<span class="bc-rating-star"></span>
			</div>
		</div>
		<div class="bc-single-product__rating--bottom">
			<span class="bc-rating-star"></span>
			<span class="bc-rating-star"></span>
			<span class="bc-rating-star"></span>
			<span class="bc-rating-star"></span>
			<span class="bc-rating-star"></span>
		</div>
	</div>
	<div class="bc-single-product__rating-reviews">
		<a
			<?php
			if ( $link ) {
				$link = amp_get_permalink( $product->post_id() ) . '#bc-single-product__reviews';
				?>
				href="<?php echo esc_url( $link ); ?>"
			<?php } ?>
			class="bc-link bc-single-product__reviews-anchor"
			data-js="bc-single-product-reviews-anchor"
		>
			<?php printf( esc_html( _n( '%d review', '%d reviews', $review_count, 'bigcommerce' ) ), intval( $review_count, 10 ) ); ?>
		</a>
	</div>
</div>