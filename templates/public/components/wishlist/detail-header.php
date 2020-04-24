<?php

use BigCommerce\Accounts\Wishlists\Wishlist;

/**
 * @var Wishlist $wishlist The wishlist to display
 * @var string   $title    The page title
 * @var string   $share    The rendered "Share" form
 * @var string   $edit     The rendered "Edit" form
 * @var string   $delete   The rendered "Delete" form
 * @var string[] $actions  The rendered action links
 * @version 1.0.0
 */

?>
<div class="bc-manage-wish-list-header">
	<h1 class="bc-manage-wish-list-title">
		<?php echo esc_html( $title ); ?>
	</h1>
	<?php echo $share; ?>

	<div class="bc-manage-wish-list-actions">
		<?php foreach ( $actions as $action ) {
			echo $action;
		} ?>
	</div>
</div>

<?php echo $edit; ?>
<?php echo $delete; ?>