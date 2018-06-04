<?php
/**
 * "View Product" Button
 *
 * @var Product $product
 * @var string  $permalink Permalink to the product
 * @var string  $label     The button label
 */

use BigCommerce\Post_Types\Product\Product;

?>

<div class="bc-product__view-product">
	<a href="<?php echo esc_url( $permalink ); ?>" class="bc-btn bc-btn--view-product">
		<?php echo esc_html( $label ); ?>
	</a>
</div>
