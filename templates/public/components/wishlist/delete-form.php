<?php

use BigCommerce\Accounts\Wishlists\Wishlist;

/**
 * @var Wishlist $wishlist    The wishlist to display
 * @var string   $nonce_field The nonce field for the delete form
 * @version 1.0.0
 */

?>
<div class="bc-wish-list-dialog-content bc-wish-list-dialog-content--delete">
	<h2 class="bc-wish-list-dialog-title">
		<?php printf( '%s <br><span class="bc-link">%s</span>?',
		esc_html( __( 'Are you sure you want to delete your Wish List', 'bigcommerce' ) ),
		esc_html( $wishlist->name() ) ); ?>
	</h2>
	<p class="bc-wish-list-dialog-description">
		<?php esc_html_e( 'This action cannot be undone.', 'bigcommerce' ); ?>
	</p>
	<form action="<?php echo esc_url( $wishlist->delete_url() ); ?>" method="post" class="bc-wish-list-dialog-form">
		<?php echo $nonce_field; ?>
		<button type="submit" class="bc-btn bc-btn--form-submit bc-btn--delete-wish-list">
			<?php esc_html_e( 'Yes', 'bigcommerce' ); ?>
		</button>
	</form>
	<button type="button" class="bc-link bc-wish-list-dialog-close" data-js="bc-wish-list-dialog-close"><?php esc_html_e( 'No, Take me back', 'bigcommerce' ); ?></button>
</div>
