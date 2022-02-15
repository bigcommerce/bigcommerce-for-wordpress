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
<!-- class="bc-product-form__control bc-product-form__control--dropdown" is required -->
<div for="option-<?php echo esc_attr( $id ); ?>" class="bc-product-form__control bc-product-form__control--dropdown">

	<span class="bc-form__label bc-product-form__option-label <?php if ( $required ) { echo esc_attr( 'bc-form-control-required' ); } ?>"><?php echo esc_html( $label ); ?></span>
	<!-- data-js="product-form-option" and data-field="product-form-option-select" are required -->
	<div class="bc-product-form__option-variants" data-js="product-form-option" data-field="product-form-option-select">
		<select name="option[<?php echo esc_attr( $id ); ?>]"
		        id="option-<?php echo esc_attr( $id ); ?>"
		        class="bc-product-variant__select"
		        data-js="bc-product-option-field"
				data-option-id="<?php echo esc_attr( $id ); ?>">
			<?php foreach ( $options as $option ) { ?>
				<option value="<?php echo esc_attr( $option['id'] ); ?>"<?php selected( $option['is_default'] ); ?>><?php echo esc_html( $option['label'] ); ?></option>
			<?php } ?>
		</select>
	</div>

</div>
