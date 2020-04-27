<?php
/**
 * "View Product" Button
 *
 * @var Product $product
 * @var string  $permalink  Permalink to the product
 * @var string  $label      The button label
 * @var string  $attributes Sanitized HTML attributes to add to the template
 * @version 1.0.0
 */

use BigCommerce\Post_Types\Product\Product;
$permalink = amp_get_permalink( $product->post_id() );
?>

<div class="bc-product__view-product">
	<a href="<?php echo esc_url( $permalink ); ?>" class="bc-btn bc-btn--view-product" <?php echo $attributes; // WPCS: XSS ok. Already escaped data. ?>>
		<?php echo esc_html( $label ); ?>
	</a>
</div>
