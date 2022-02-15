<?php
/**
 * Display the fields to select options for a product
 *
 * @var string  $id
 * @var string  $label
 * @var array[] $options
 * @var bool    $required
 * @version 1.0.0
 */

?>

<!-- class="bc-product-form__control bc-product-form__control--radio" is required -->
<div id="option-<?php echo esc_attr( $id ); ?>" class="bc-product-form__control bc-product-form__control--radio">
	<span class="bc-form__label bc-product-form__option-label <?php if ( $required ) { echo esc_attr( 'bc-form-control-required' ); } ?>"><?php echo esc_html( $label ); ?></span>
	<!-- data-js="product-form-option" and data-field="product-form-option-radio" are required -->
	<div class="bc-product-form__option-variants" data-js="product-form-option" data-field="product-form-option-radio">
		<?php foreach ( $options as $key => $option ) { ?>
			<label for="option--<?php echo esc_attr( $option['id'] ); ?>" class="bc-product-variant__label">
				<input type="radio"
				       name="option[<?php echo esc_attr( $id ); ?>]"
				       data-option-id="<?php echo esc_attr( $id ); ?>"
				       data-js="bc-product-option-field"
				       id="option--<?php echo esc_attr( $option['id'] ); ?>"
				       value="<?php echo esc_attr( $option['id'] ); ?>"
				       class="bc-product-variant__radio"
					<?php if ( 0 === $key && $required ) {?>
						required="required"
					<?php } ?>
					<?php checked( $option['is_default'] ); ?> />

				<span class="bc-product-variant__label--radio">
				<span class="bc-product-variant__label--title">
					<?php echo esc_html( $option['label'] ); ?>
				</span>
			</span>
			</label>

		<?php } ?>
	</div>
</div>
