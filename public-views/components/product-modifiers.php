<?php
/**
 * Display the fields to select options for a product
 *
 * @var Product  $product
 * @var string[] $modifiers The rendered markup for each modifier
 */

use BigCommerce\Post_Types\Product\Product;

?>

<div class="bc-product-form__modifiers" data-js="product-modifiers">
	<?php
	foreach ( $modifiers as $modifier ) {
		echo $modifier;
	}
	?>
</div>
