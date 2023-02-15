<?php
/**
 * @var array  $enabled_currencies
 * @var string $selected_currency
 *
 * @version 1.0.0
 */
?>

<section class="bc-currency-switcher">
	<!-- data-js="bc-dynamic-fields" is required -->
	<form class="bc-form" data-js="bc-dynamic-fields" method="post">
		<?php wp_nonce_field( 'switch-currency' ); ?>
		<input type="hidden" name="bc-action" value="switch-currency"/>

		<div class="bc-form__control">
			<label for="bc-currency-code"><?php esc_html_e( 'Currency Switcher', 'bigcommerce' ); ?></label>
			<select name="bc-currency-code">
				<?php foreach ( $enabled_currencies as $currency ): ?>
					<option value="<?php echo esc_html( $currency['currency_code'] ); ?>" <?php selected( $selected_currency, $currency['currency_code'] ); ?>><?php printf('%s (%s)', esc_html( $currency['name'] ), esc_html( $currency['token'] ) ); ?></option>
				<?php endforeach; ?>
			</select>
		</div>

		<div class="bc-form__actions">
			<button class="bc-btn bc-btn--small bc-btn--form-submit" aria-label="<?php esc_html_e( 'Change Your Currency', 'bigcommerce' ); ?>" type="submit"><?php esc_html_e( 'Apply', 'bigcommerce' ); ?></button>
		</div>
	</form>
</section>
