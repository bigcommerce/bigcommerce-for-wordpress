<?php
/**
 * Display the checkbox product option field for a product
 *
 * @var string $id
 * @var string $label
 * @var bool   $required
 * @var bool   $checked
 * @var string $checkbox_label
 * @var string $checkbox_value
 * @version 1.0.0
 */

?>

<div class="bc-product-form__control bc-product-form__control--checkbox">

	<span class="bc-form__label bc-product-form__option-label <?php if ( $required ) { echo esc_attr( 'bc-form-control-required' ); } ?>">
		<?php echo esc_html( $label ); ?>
	</span>

	<div class="bc-product-form__option-field" data-js="product-form-option" data-field="product-form-option-checkbox">

		<label for="option-<?php echo esc_attr( $checkbox_value ); ?>" class="bc-product-option__label">
			<input type="checkbox" name="option[<?php echo esc_attr( $id ); ?>]"
						 id="option-<?php echo esc_attr( $checkbox_value ); ?>"
						 class="bc-product-option__text"
						 data-js="bc-product-option-field"
						 data-option-id="<?php echo esc_attr( $id ); ?>"
						 value="<?php echo esc_attr( $checkbox_value ); ?>"
						 <?php if ( $required ) { echo 'required="required"'; } ?>
						 <?php if ( $checked ) { echo 'checked="checked"'; } ?>
			/>

			<span class="bc-product-option__label--checkbox">
				<span class="bc-product-option__label--title">
					<?php echo esc_html( $checkbox_label ); ?>
				</span>
			</span>
		</label>
	</div>

</div>
