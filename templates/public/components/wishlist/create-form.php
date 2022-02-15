<?php

/**
 * @var string $action_url  The form action URL
 * @var string $nonce_field The nonce field for the form
 * @var int[]  $products    The IDs of products to add to the new list
 * @version 1.0.0
 */

?>
<div class="bc-wish-list-dialog-content">
	<h2 class="bc-wish-list-dialog-title"><?php esc_html_e( 'New Wish List', 'bigcommerce' ); ?></h2>
	<p class="bc-wish-list-dialog-description">
		<?php esc_html_e( 'Give your Wish List a name and set its public visibility.', 'bigcommerce' ); ?>
	</p>
	<form action="<?php echo esc_url( $action_url ); ?>" method="post" class="bc-wish-list-dialog-form">
		<?php echo $nonce_field; ?>
		<input type="hidden" name="items" value="<?php echo implode(',', array_map( 'intval', $products ) ); ?>" />
		<label for="wish-list-name-new"><?php esc_html_e( 'Wish List Name', 'bigcommerce' ); ?></label>
		<input
			type="text"
			id="wish-list-name-new"
			class="bc-wish-list-name-field"
			name="name"
			value=""
			data-default-value=""
		/>

		<input type="checkbox" name="public" value="1" id="wish-list-public-new" class="bc-wish-list-public-field" />
		<label for="wish-list-public-new" class="bc-wish-list-public-label"><?php esc_html_e( 'Make this Wish List shareable with a public link?', 'bigcommerce' ); ?></label>

		<button type="submit" class="bc-btn bc-btn--form-submit">
			<?php esc_html_e( 'Create Wish List', 'bigcommerce' ); ?>
		</button>
	</form>
</div>
