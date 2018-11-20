<?php
/**
 * Display the fields to select options for a product
 *
 * @var Product  $product
 * @var string[] $modifiers The rendered markup for each modifier
 */

use BigCommerce\Post_Types\Product\Product;

foreach ( $modifiers as $modifier ) {
	echo $modifier;
}
