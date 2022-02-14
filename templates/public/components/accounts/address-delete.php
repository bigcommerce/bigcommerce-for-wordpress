<?php
/**
 * The form to delete an address
 *
 * @var int    $address_id
 * @var string $action
 * @var string $url
 * @version 1.0.0
 */
?>
<!-- class="bc-account-address__delete-form" is required -->
<form action="<?php echo esc_url( $url ); ?>" method="post" class="bc-account-address__delete-form">
	<input type="hidden" value="<?php echo esc_attr( $address_id ); ?>" name="address-id"/>
	<input type="hidden" value="<?php echo esc_attr( $action ); ?>" name="bc-action"/>
	<?php // note: can't use `wp_nonce_field` here because it doesn't use a unique id and this template is used various times on the same page ?>
	<input type="hidden" name="_wpnonce" value="<?php echo wp_create_nonce( $action . $address_id ) ?>">
	<?php echo wp_referer_field( false ) ?>
	<!-- data-js="bc-account-address-delete" is required -->
	<button type="submit" class="bc-button bc-account-address__delete" data-js="bc-account-address-delete" data-deleteurl="<?php echo esc_url( $url ); ?>"><?php echo esc_html( __( 'Delete', 'bigcommerce' ) ); ?></button>
</form>
