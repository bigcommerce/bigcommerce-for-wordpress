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
 */
?>
<div class="bc-product-single">
	<section class="bc-product-single__top">
		<?php echo $images; ?>

		<div class="bc-product-single__meta">
			<?php echo $title; ?>
			<?php echo $brand; ?>
			<?php echo $price; ?>
			<?php echo $rating; ?>

			<?php if ( $sku ) { ?>
				<span class="bc-product__sku">
					<span class="bc-product-single__meta-label">
						<?php esc_html_e( 'SKU:', 'bigcommerce' ); ?>
					</span>
					<?php echo esc_html( $sku ); ?>
				</span>
			<?php } ?>
			<?php echo $form; ?>
		</div>
	</section>

	<section class="bc-single-product__description">
		<h4 class="bc-single-product__section-title"><?php echo esc_html__( 'Product Description', 'bigcommerce' ); ?></h4>
		<?php echo $description; ?>
	</section>

	<?php echo $specs; ?>

	<?php echo $reviews; ?>

	<?php echo $related; ?>

</div>
