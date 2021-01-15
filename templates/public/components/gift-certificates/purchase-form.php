<?php
/**
 * Renders the gift certificate purchase form.
 *
 * @var string[] $errors
 * @var string[] $defaults
 * @var string   $button_label The label of the purchase button
 * @version 1.0.0
 */
$error_class = 'bc-form__control--error'; // REQUIRED

?>
<section class="bc-gift-page">
	<div class="bc-gift-purchase__form">
		<form class="bc-form bc-form--gift-purchase <?php if ( ! empty( $errors ) ) { echo esc_attr( 'bc-form--has-errors' ); } ?>" method="post">
			<?php wp_nonce_field( 'gift-purchase' ); ?>
			<input type="hidden" name="bc-action" value="gift-purchase" />

			<div class="bc-form__row">
				<div class="bc-form__col">
					<fieldset class="bc-form__field-group">
						<label for="bc-gift-purchase-sender-name" class="bc-form__control <?php if ( in_array( 'sender-name', $errors ) ) { echo esc_attr( $error_class ); } ?>">
							<span class="bc-form__label bc-gift-purchase__form-label bc-form-control-required"><?php echo esc_html( __( 'Your Name', 'bigcommerce' ) ); ?></span>
							<input type="text" name="bc-gift-purchase[sender-name]" id="bc-gift-purchase-sender-name" value="<?php echo esc_attr( $defaults[ 'sender-name' ] ); ?>" data-form-field="bc-form-field-sender-name" />
						</label>
						<label for="bc-gift-purchase-sender-email" class="bc-form__control <?php if ( in_array( 'sender-email', $errors ) ) { echo esc_attr( $error_class ); } ?>">
							<span class="bc-form__label bc-gift-purchase__form-label bc-form-control-required"><?php echo esc_html( __( 'Your Email', 'bigcommerce' ) ); ?></span>
							<input type="text" name="bc-gift-purchase[sender-email]" id="bc-gift-purchase-sender-email" value="<?php echo esc_attr( $defaults[ 'sender-email' ] ); ?>" data-form-field="bc-form-field-sender-email" />
						</label>
					</fieldset>
					<fieldset class="bc-form__field-group">
						<label for="bc-gift-purchase-recipient-name" class="bc-form__control <?php if ( in_array( 'recipient-name', $errors ) ) { echo esc_attr( $error_class ); } ?>">
							<span class="bc-form__label bc-gift-purchase__form-label bc-form-control-required"><?php echo esc_html( __( "Recipient's Name", 'bigcommerce' ) ); ?></span>
							<input type="text" name="bc-gift-purchase[recipient-name]" id="bc-gift-purchase-recipient-name" value="<?php echo esc_attr( $defaults[ 'recipient-name' ] ); ?>" data-form-field="bc-form-field-recipient-name" />
						</label>
						<label for="bc-gift-purchase-recipient-email" class="bc-form__control <?php if ( in_array( 'recipient-email', $errors ) ) { echo esc_attr( $error_class ); } ?>">
							<span class="bc-form__label bc-gift-purchase__form-label bc-form-control-required"><?php echo esc_html( __( "Recipient's Email", 'bigcommerce' ) ); ?></span>
							<input type="text" name="bc-gift-purchase[recipient-email]" id="bc-gift-purchase-recipient-email" value="<?php echo esc_attr( $defaults[ 'recipient-email' ] ); ?>" data-form-field="bc-form-field-recipient-email" />
						</label>
					</fieldset>
				</div>

				<div class="bc-form__col">
					<label for="bc-gift-purchase-amount" class="bc-form__control <?php if ( in_array( 'amount', $errors ) ) { echo esc_attr( $error_class ); } ?>">
						<span class="bc-form__label bc-gift-purchase__form-label bc-form-control-required"><?php echo esc_html( __( 'Amount', 'bigcommerce' ) ); ?></span>
						<input type="text" name="bc-gift-purchase[amount]" id="bc-gift-purchase-amount" value="<?php echo esc_attr( $defaults[ 'amount' ] ); ?>" data-form-field="bc-form-field-amount" />
					</label>
					<label for="bc-gift-purchase-message" class="bc-form__control <?php if ( in_array( 'message', $errors ) ) { echo esc_attr( $error_class ); } ?>">
						<span class="bc-form__label bc-gift-purchase__form-label"><?php echo esc_html( __( 'Optional Message', 'bigcommerce' ) ); ?></span>
						<textarea name="bc-gift-purchase[message]" id="bc-gift-purchase-message" data-form-field="bc-form-field-message"><?php echo esc_textarea( $defaults[ 'message' ] ); ?></textarea>
					</label>

					<?php if ( count( $themes ) === 1 ) : ?>
						<input type="hidden" name="bc-gift-purchase[theme]" id="bc-gift-purchase-theme" data-form-field="bc-form-field-theme" value="<?php echo esc_attr( reset( $themes )['template'] ); ?>">
					<?php else: ?>
						<label for="bc-gift-purchase-theme" class="bc-form__control <?php if ( in_array( 'theme', $errors ) ) { echo esc_attr( $error_class ); } ?>">
							<span class="bc-form__label bc-gift-purchase__form-label"><?php echo esc_html( __( 'Gift Certificate Theme', 'bigcommerce' ) ); ?></span>
							<select name="bc-gift-purchase[theme]" id="bc-gift-purchase-theme" data-form-field="bc-form-field-theme">
								<?php foreach ( $themes as $theme ) : ?>
									<option value="<?php echo esc_attr( $theme['template'] ); ?>" <?php selected( $theme['template'], $defaults['theme'], true ); ?>><?php echo esc_html( $theme['name'] ); ?></option>
								<?php endforeach; ?>
							</select>
						</label>
					<?php endif; ?>
				</div>
			</div>

			<div class="bc-form-terms">
				<label for="bc-gift-purchase-terms" class="bc-form__control bc-form__control--checkbox <?php if ( in_array( 'terms', $errors ) ) { echo esc_attr( $error_class ); } ?>">
					<input type="checkbox" name="bc-gift-purchase[terms]" id="bc-gift-purchase-terms" value="1" data-form-field="bc-form-field-terms" />
					<span class="bc-form__label bc-gift-purchase__form-label bc-form-control-required"><?php echo esc_html( __( 'I agree that Gift Certificates are nonrefundable.', 'bigcommerce' ) ); ?></span>
				</label>
			</div>

			<div class="bc-form__actions bc-form__actions--left bc-gift-purchase__actions">
				<button class="bc-btn bc-gift-purchase-form-submit" aria-label="<?php esc_attr( $button_label ); ?>" type="submit" data-js="bc-gift-purchase-form-save"><?php echo esc_html( $button_label ); ?></button>
			</div>
		</form>
	</div>
</section>
