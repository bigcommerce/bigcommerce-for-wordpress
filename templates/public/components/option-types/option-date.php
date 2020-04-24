<?php
/**
 * Display the date product option field for a product
 *
 * @var string $id
 * @var string $label
 * @var bool   $required
 * @var int    $min_value
 * @var int    $max_value
 * @var string $default_value
 * @version 1.0.0
 */

?>

<div class="bc-product-form__control bc-product-form__control--date">
	<label class="bc-form__label bc-product-form__option-label <?php if ( $required ) { echo 'bc-form-control-required'; } ?>" for="option-<?php echo esc_attr( $id ); ?>">
		<?php echo esc_html( $label ); ?>
	</label>

	<div class="bc-product-form__option-field">
		<input type="date" name="option[<?php echo esc_attr( $id ); ?>]"
		       id="option-<?php echo esc_attr( $id ); ?>"
		       class="bc-product-option__date"
		       data-js="bc-product-option-field"
		       data-option-id="<?php echo esc_attr( $id ); ?>"
		       <?php if ( $required ) { echo 'required="required"'; } ?>
		       <?php if ( ! empty( $min_value ) ) { printf( 'min="%s"', esc_attr( $min_value ) ); } ?>
		       <?php if ( ! empty( $max_value ) ) { printf( 'max="%s"', esc_attr( $max_value ) ); } ?>
		       value="<?php echo esc_attr( $default_value ); ?>"
		/>
	</div>
</div>
