<?php

/**
 * @var string[] $wishlists   An array of wishlist rows
 * @var string   $create_list The form to create a new wishlist
 * @version 1.0.0
 */

?>
<div class="bc-wish-list-header">
	<?php if ( ! empty( $wishlists) ) { ?>
		<div class="bc-wish-list-header-title bc-wish-list-name"><?php esc_html_e( 'Wish List Name', 'bigcommerce' ); ?></div>
		<div class="bc-wish-list-header-title bc-wish-list-item-count"><?php esc_html_e( 'Items', 'bigcommerce' ); ?></div>
		<div class="bc-wish-list-header-title bc-wish-list-shared"><?php esc_html_e( 'Shared', 'bigcommerce' ); ?></div>
		<div class="bc-wish-list-header-title bc-wish-list-actions u-bc-visual-hide"><?php esc_html_e( 'Actions', 'bigcommerce' ); ?></div>
	<?php } else { ?>
		<h2><?php esc_html_e( 'You currently have no Wish Lists. Would you like to create one?', 'bigcommerce' ); ?></h2>
	<?php } ?>
</div>

<div class="bc-wish-list-body">
	<?php foreach ( $wishlists as $list ) { ?>
		<?php echo $list; ?>
	<?php } ?>
</div>

<?php echo $create_list; ?>
