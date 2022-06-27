<?php
/**
 * Component: Inventory Level
 *
 * @description Display a message for the current inventory level.
 *
 * @var Product $product
 * @var string  $status The inventory status of the product
 * @var string  $label  The label for the current stock level
 * @version 1.0.0
 */

use BigCommerce\Post_Types\Product\Product;

?>

<span class="bc-product__inventory bc-product__<?php echo sanitize_html_class( $status ); ?>">(<?php echo esc_html( $label ); ?>)</span>
