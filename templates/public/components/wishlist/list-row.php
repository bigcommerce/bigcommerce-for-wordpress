<?php

use BigCommerce\Accounts\Wishlists\Wishlist;

/**
 * @var Wishlist $wishlist  The wishlist for the row
 * @var string   $name      The wishlist name
 * @var int      $count     The number of items in the wishlist
 * @var bool     $is_public Whether the wishlist is public
 * @var string   $user_url  The URL to the user's page for the list
 * @var string   $share_url The URL to share a public wishlist
 * @var string   $edit      The template for the wishlist's edit form
 * @var string   $delete    The template for the wishlist's delete form
 * @var string[] $actions   Action links for the list
 * @version 1.0.0
 */

?>
<div class="bc-wish-list-item bc-wish-list-name">
	<span class="bc-small-screen-title"><?php esc_html_e( 'Wish List Name: ', 'bigcommerce' ); ?></span>
	<a href="<?php echo esc_url( $user_url ); ?>" class="bc-link bc-wish-list-link">
		<?php echo esc_html( $name ); ?>
	</a>
</div>
<div class="bc-wish-list-item bc-wish-list-item-count">
	<span class="bc-small-screen-title"><?php esc_html_e( 'Items: ', 'bigcommerce' ); ?></span>
	<?php echo (int) $count; ?>
</div>
<div class="bc-wish-list-item bc-wish-list-shared">
	<?php if ( $is_public ) { ?>
		<span class="bc-small-screen-title"><?php esc_html_e( 'Shared: ', 'bigcommerce' ); ?></span>
		<?php esc_html_e( 'Yes', 'bigcommerce' ); ?>
	<?php } ?>
</div>
<div class="bc-wish-list-item bc-wish-list-actions">
	<span class="bc-small-screen-title"><?php esc_html_e( 'Actions: ', 'bigcommerce' ); ?></span>
	<?php if ( $is_public ) { ?>
		<a href="<?php echo esc_url( $share_url ); ?>" class="bc-link bc-wishilist-share" data-js="bc-share-wish-list">
			<?php esc_html_e( 'Share', 'bigcommerce' ); ?>
		</a>
	<?php } ?>
	<?php foreach ( $actions as $action ) { ?>
		<?php echo $action; ?>
	<?php } ?>
</div>
<?php echo $edit; ?>
<?php echo $delete; ?>
