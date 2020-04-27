<?php
/**
 * Renders the gift certificate balance shortcode.
 *
 * @var string $form         The HTML for the form to check the balance
 * @var string $response     The HTML for the balance. Empty when the form has not been submitted.
 * @var string $instructions The HTML for the redemption instructions
 * @version 1.0.0
 */
?>
<div class="bc-gift-page">
	<div class="bc-gift-balance">
		<?php echo $form; ?>
		<?php echo $response; ?>
	</div>
</div>
<?php echo $instructions; ?>
