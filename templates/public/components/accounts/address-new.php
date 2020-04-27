<?php
/**
 * Add a new address to the user's account
 *
 * @var string $form
 * @version 1.0.0
 */
?>

<!-- This button element has to be the first element in this template -->
<button class="bc-account-addresses__add-button" type="button" data-js="bc-account-address-add-new-trigger" data-content="" data-target=""><i class="bc-icon icon-bc-plus"></i> <?php echo esc_html( __( 'New Address', 'bigcommerce' ) ); ?></button>

<!-- This script tag and the blank data-js attribute are required -->
<script type="text/template" data-target="bc-account-address-new-form" data-js="">
	<?php echo $form; ?>
</script>
