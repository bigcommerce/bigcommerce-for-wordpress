<?php

/**
 * The template for displaying the related products grid
 *
 * @var string[] $products An array of rendered related products
 * @var string $columns number of columns to display for products grid.
 * @version 1.0.0
 */

?>

<section class="bc-single-product__related">
	<h3 class="bc-single-product__section-title--related"><?php echo esc_html__( 'Related Products', 'bigcommerce' ); ?></h3>
	<!-- class="bc-product-grid" is required -->
	<div class="bc-product-grid bc-product-grid--related bc-product-grid--<?php echo intval( $columns ); ?>col">
		<?php
		foreach ( $products as $product ) {
			echo $product;
		}
		?>
	</div>
</section>
