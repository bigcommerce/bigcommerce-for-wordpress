<?php
/**
 * @var string $images
 * @var string $title
 * @var string $brand
 * @var string $price
 * @var string $rating
 * @var string $form
 * @var string $description
 * @var string $sku
 * @var string $specs
 * @var string $related
 * @var string $reviews
 * @version 1.0.0
 */
?>
<div class="bc-product-single" data-js="bc-product-single">
	<section class="bc-product-single__top">

		<div class="bc-product__gallery">
			<?php echo $images; // WPCS: XSS ok. Already escaped data. ?>
		</div>

		<div class="bc-product-single__meta">
			<?php echo $title; // WPCS: XSS ok. Already escaped data. ?>
			<?php echo $brand; // WPCS: XSS ok. Already escaped data. ?>
			<?php echo $price; // WPCS: XSS ok. Already escaped data. ?>
			<?php echo $rating; // WPCS: XSS ok. Already escaped data. ?>

			<?php if ( $sku ) { ?>
				<span class="bc-product__sku">
					<span class="bc-product-single__meta-label">
						<?php esc_html_e( 'SKU:', 'bigcommerce' ); ?>
					</span>
					<?php echo esc_html( $sku ); ?>
				</span>
			<?php } ?>
			<?php echo $form; // WPCS: XSS ok. Already escaped data. ?>
		</div>
	</section>

	<section class="bc-single-product__description">
		<h4 class="bc-single-product__section-title"><?php echo esc_html__( 'Product Description', 'bigcommerce' ); ?></h4>
		<?php echo $description; // WPCS: XSS ok. Already escaped data. ?>
	</section>

	<?php echo $specs; // WPCS: XSS ok. Already escaped data. ?>

	<?php echo $reviews; // WPCS: XSS ok. Already escaped data. ?>

	<?php echo $related; // WPCS: XSS ok. Already escaped data. ?>

</div>
