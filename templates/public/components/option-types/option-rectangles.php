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

<!-- class="bc-product-form__control bc-product-form__control--rectangle" is required -->
<div id="option-<?php echo esc_attr( $id ); ?>" class="bc-product-form__control bc-product-form__control--rectangle">

	<span class="bc-form__label bc-product-form__option-label <?php if ( $required ) { echo esc_attr( 'bc-form-control-required' ); } ?>"><?php echo esc_html( $label ); ?></span>

	<!-- data-js="product-form-option" and data-field="product-form-option-radio" are required -->
	<div class="bc-product-form__option-variants bc-product-form__option-variants--inline" data-js="product-form-option" data-field="product-form-option-radio">
		<?php foreach ( $options as $key => $option ) { ?>
			<input type="radio"
				name="option[<?php echo esc_attr( $id ); ?>]"
				id="option--<?php echo esc_attr( $option['id'] ); ?>"
				value="<?php echo esc_attr( $option['id'] ); ?>"
				class="u-bc-visual-hide bc-product-variant__radio--hidden"
				data-option-id="<?php echo esc_attr( $id ); ?>"
				data-js="bc-product-option-field"
				<?php if ( 0 === $key && $required ) { echo 'required="required"'; } ?>
				<?php checked( $option['is_default'] ); ?>
			>
			<label for="option--<?php echo esc_attr( $option['id'] ); ?>" class="bc-product-variant__label">
				<span class="bc-product-variant__label--rectangle">
					<?php echo esc_html( $option['label'] ); ?>
				</span>
			</label>
		<?php } ?>
	</div>

</div>
