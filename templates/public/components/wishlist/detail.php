<?php

use BigCommerce\Accounts\Wishlists\Wishlist;

/**
 * @var Wishlist $wishlist   The wishlist to display
 * @var string[] $products   The rendered product rows
 * @var string   $breadcrumb The rendered breadcrumb HTML
 * @var string   $header     The rendered header HTML
 * @version 1.0.0
 */

?>
<?php echo $breadcrumb; ?>
<?php echo $header; ?>

<ul class="bc-manage-wish-list-products">
	<?php foreach ( $products as $product ) { ?>
		<li class="bc-manage-wish-list-product">
			<?php echo $product; ?>
		</li>
	<?php } ?>
</ul>
