<?php
/**
 * Actions to take on an existing address
 *
 * @var string $form
 * @var string $delete_form
 */
?>
<div class="bc-account-address__actions" data-js="bc-account-address-actions">
	<button class="bc-account-address__edit" data-js="bc-account-address-edit-trigger" data-content=""
					data-target=""><?php echo esc_html( __( 'Edit', 'bigcommerce' ) ); ?></button>

	<script type="text/template" data-js="">

		<?php echo $form; ?>

	</script>

	<?php echo $delete_form; ?>
</div>
