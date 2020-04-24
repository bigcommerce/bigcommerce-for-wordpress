<?php
/**
 * Actions to take on an existing address
 *
 * @var string $form
 * @var string $delete_form
 * @version 1.0.0
 */
?>

<!-- This button has to be the first element of the template. -->
<button class="bc-account-address__edit" data-js="bc-account-address-edit-trigger" data-content=""
				data-target=""><?php echo esc_html( __( 'Edit', 'bigcommerce' ) ); ?></button>

<!-- This script tag and empty data-js="" attribute are required -->
<script type="text/template" data-js="">

	<?php echo $form; ?>

</script>

<?php echo $delete_form; ?>
