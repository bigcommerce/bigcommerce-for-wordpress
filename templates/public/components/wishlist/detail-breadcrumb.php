<?php

use BigCommerce\Accounts\Wishlists\Wishlist;

/**
 * @var Wishlist $wishlist       The wishlist to display
 * @var string   $wish_lists_url The url to all My Account Wish Lists
 * @version 1.0.0
 */

?>
<div class="bc-wish-list-breadcrumbs">
	<a href="<?php echo esc_url( $wish_lists_url ); ?>" class="bc-link bc-all-wish-lists">
		<?php esc_html_e( '&lt; All Wish Lists', 'bigcommerce' ); ?>
	</a>
</div>
