<?php
/**
 * Display the checkbox product modifier field for a product
 *
 * @var string $id
 * @var string $label
 * @var bool   $required
 * @var bool   $checked
 * @var string $checkbox_label
 * @var string $checkbox_value
 */

?>

<div class="bc-product-form__control bc-product-form__control--checkbox">

	<span class="bc-form__label bc-product-form__modifier-label <?php if ( $required ) { echo esc_attr( 'bc-form-control-required' ); } ?>">
		<?php echo esc_html( $label ); ?>
	</span>

	<div class="bc-product-form__modifier-field">

		<label for="modifier-<?php echo esc_attr( $checkbox_value ); ?>" class="bc-product-modifier__label">
			<input type="checkbox" name="modifier[<?php echo esc_attr( $id ); ?>]"
						 id="modifier-<?php echo esc_attr( $checkbox_value ); ?>"
						 class="bc-product-modifier__text"
						 data-modifier-id="<?php echo esc_attr( $id ); ?>"
						 value="<?php echo esc_attr( $checkbox_value ); ?>"
						 <?php if ( $required ) { echo 'required="required"'; } ?>
						 <?php if ( $checked ) { echo 'checked="checked"'; } ?>
			/>

			<span class="bc-product-modifier__label--checkbox">
				<span class="bc-product-modifier__label--title">
					<?php echo esc_html( $checkbox_label ); ?>
				</span>
			</span>
		</label>
	</div>

</div>
