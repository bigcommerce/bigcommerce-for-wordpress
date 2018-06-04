<?php
/**
 * Product Card for the Admin UI Dialog
 *
 * @package BigCommerce Admin
 *
 * @var int $post_id
 * @var int $bigcommerce_id
 * @var string $date
 * @var string $date_gmt
 * @var string $title
 * @var string $content
 * @var array $image
 * @var string $sku
 * @var string $price_range
 * @var array $bigcommerce_brand
 * @var array $bigcommerce_flag
 * @var array $bigcommerce_category
 * @var string $placeholder_image
 */

$image_size = 'thumbnail';
?>
<div class="bc-shortcode-ui__product">
	<a href="#" class="bc-shortcode-ui__product-anchor">
		<div class="bc-shortcode-ui__product-inner">
			<figure class="bc-shortcode-ui__product-image">
				<?php if ( ! empty( $image[ 'sizes' ][ $image_size ][ 'url' ] ) ) { ?>
					<img src="<?php echo esc_url( $image[ 'sizes' ][ $image_size ][ 'url' ] ); ?>" alt="<?php echo esc_attr( $title ); ?>" class="bc-shortcode-ui__product-placeholder">
				<?php } else { ?>
					<img src="<?php echo esc_url( $placeholder_image ); ?>" alt="<?php echo esc_attr( $title ); ?>" class="bc-shortcode-ui__product-placeholder">
				<?php } ?>
			</figure>
			<div class="bc-shortcode-ui__product-meta">
				<h3 class="bc-shortcode-ui__product-title"><?php echo esc_html( $title ); ?></h3>
				<span class="bc-shortcode-ui__product-price"><?php echo esc_html( $price_range ); ?></span>
			</div>
			<div class="bc-shortcode-ui__product-description"><?php echo wp_trim_words( $content, 10, '...' ); ?></div>
		</div>
		<div class="bc-shortcode-ui__product-actions">
			<button class="button button-primary" type="button" data-js="add-product" data-postID="<?php echo intval( $post_id ); ?>" data-bcid="<?php echo intval( $bigcommerce_id ); ?>">
				<?php esc_html_e( 'Add Product', 'bigcommerce' ); ?>
			</button>

			<button class="button button-primary" type="button" data-js="remove-product" data-postID="<?php echo intval( $post_id ); ?>" data-bcid="<?php echo intval( $bigcommerce_id ); ?>">
				<?php esc_html_e( 'Remove Product', 'bigcommerce' ); ?>
			</button>
		</div>
	</a>
</div>