<?php
/**
 * Add a new address to the user's account
 *
 * @var string $form
 */
?>

<div class="bc-account-address__actions bc-account-address__actions--new" data-js="bc-account-address-actions">
	<button class="bc-account-addresses__add-button" type="button" data-js="bc-account-address-add-new-trigger" data-content="" data-target=""><i class="bc-icon icon-bc-plus"></i> <?php echo esc_html( __( 'New Address', 'bigcommerce' ) ); ?></button>

	<script type="text/template" data-target="bc-account-address-new-form" data-js="">
		<?php echo $form; ?>
	</script>
</div>
