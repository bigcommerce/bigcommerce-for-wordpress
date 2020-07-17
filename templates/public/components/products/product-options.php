<?php
/**
 * Display the fields to select options for a product
 *
 * @package BigCommerce
 *
 * @var Product  $product
 * @var string[] $options  The rendered markup for each option
 * @var array    $variants Data about the variants available on the product
 * @version 1.0.0
 */

use BigCommerce\Post_Types\Product\Product;

?>

<!-- data-js="product-variants-object" and the data-variants attributes are required! -->
<div data-js="product-variants-object" data-variants="<?php echo esc_attr( wp_json_encode( $variants ) ); ?>"></div>
<?php
foreach ( $options as $option ) {
	echo $option;
}
?>
