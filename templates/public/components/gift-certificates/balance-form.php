<?php
/**
 * Renders the gift certificate balance form.
 *
 * @var string[] $errors
 * @var string[] $defaults
 * @version 1.0.0
 */
$error_class = 'bc-form__control--error'; // REQUIRED

?>
<div class="bc-gift-balance__form">
	<h3><?php echo esc_html( __( 'Check Gift Certificate Balance', 'bigcommerce' ) ); ?></h3>
	<p><?php echo esc_html( __( 'You can check the balance of a gift certificate by typing the code into the box below.', 'bigcommerce' ) ); ?></p>
	<form class="bc-form bc-form--gift-balance <?php if ( ! empty( $errors ) ) { echo esc_attr( 'bc-form--has-errors' ); } ?>" method="get">
		<label for="bc-gift-balance-code" class="bc-form__control <?php if ( in_array( 'code', $errors ) ) { echo esc_attr( $error_class ); } ?>">
			<span class="bc-form__label bc-gift-balance__form-label bc-form-control-required"><?php echo esc_html( __( 'Gift Certificate Code', 'bigcommerce' ) ); ?></span>
			<input type="text" name="bc-gift-balance[code]" id="bc-gift-balance-code" value="<?php echo esc_attr( $defaults[ 'code' ] ); ?>" data-form-field="bc-form-field-code">
		</label>

		<div class="bc-form__actions bc-form__actions--left bc-gift-balance__actions">
			<button class="bc-btn bc-gift-balance-form-submit" aria-label="<?php esc_html_e( 'Check Balance', 'bigcommerce' ); ?>" type="submit" data-js="bc-gift-balance-form-save"><?php echo esc_html( __( 'Check Balance', 'bigcommerce' ) ); ?></button>
		</div>
	</form>
</div>
