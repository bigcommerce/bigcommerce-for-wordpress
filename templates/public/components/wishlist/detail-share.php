<?php

use BigCommerce\Accounts\Wishlists\Actions\Edit_Wishlist;
use BigCommerce\Accounts\Wishlists\Wishlist;

/**
 * @var Wishlist $wishlist   The wishlist to display
 * @var string   $public_url The wishlist's public URL
 */

?>
<label for="bc-wish-list-share" class="bc-wish-list-share-title">
	<?php _e( 'Share:', 'bigcommerce' ); ?>
</label>
<input
	type="text"
	class="bc-wish-list-share"
	id="bc-wish-list-share"
	value="<?php echo esc_url( $public_url ); ?>"
/>
<!-- data-js="bc-copy-wishlist-url" is required -->
<button type="button" class="bc-wish-list-share--copy" data-js="bc-copy-wishlist-url">
	<?php _e( 'Copy', 'bigcommerce' ); ?>
</button>
