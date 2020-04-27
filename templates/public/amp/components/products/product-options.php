<?php
/**
 * Display the fields to select options for a product
 *
 * @var Product  $product
 * @var string[] $options  The rendered markup for each option
 * @var array    $variants Data about the variants available on the product
 * @version 1.0.0
 */

use BigCommerce\Post_Types\Product\Product;
$variants['allVariants'] = $variants;
?>
<div class="bc-product-form__options" data-js="product-options">
	<?php
	foreach ( $options as $option ) {
		echo $option; // WPCS: XSS ok. Already escaped data.
	}
	?>
	<amp-state id="variants<?php echo esc_attr( $product->post_id() ); ?>">
		<?php printf( '<script type="application/json">%s</script>', wp_json_encode( $variants ) ); ?>
	</amp-state>
</div>
