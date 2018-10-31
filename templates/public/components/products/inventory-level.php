<?php
/**
 * Component: Inventory Level
 *
 * @description Display a message for low stock or out of stock.
 *
 * @var Product $product
 */

use BigCommerce\Post_Types\Product\Product;

if ( $product->out_of_stock() ) { ?>
	<span class="bc-product__inventory bc-product__out_of_stock"><?php esc_html_e( '(Out of Stock)', 'bigcommerce' ); ?></span>
<?php } elseif ( $product->low_inventory() ) {
	$inventory = $product->get_inventory_level();
	?>
	<span class="bc-product__inventory bc-product__low_inventory" ><?php echo esc_html( sprintf( _n( '(%d in Stock)', '(%d in stock)', $inventory, 'bigcommerce' ), $inventory ) ); ?></span>
<?php } ?>
