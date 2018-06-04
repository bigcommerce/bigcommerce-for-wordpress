<?php
/**
 * Display the fields to select options for a product
 *
 * @var string  $id
 * @var string  $label
 * @var array[] $options
 */

?>

<div for="option-<?php echo esc_attr( $id ); ?>" class="bc-product-form__control bc-product-form__control--dropdown">

	<span class="bc-product-form__option-label"><?php echo esc_html( $label ); ?></span>

	<div class="bc-product-form__option-variants" data-js="product-form-option" data-field="product-form-option-select">
		<select name="option-<?php echo esc_attr( $id ); ?>"
		        id="option-<?php echo esc_attr( $id ); ?>"
		        class="bc-product-variant__select"
				data-option-id="<?php echo esc_attr( $id ); ?>">
			<?php foreach ( $options as $option ) { ?>
				<option value="<?php echo esc_attr( $option['id'] ); ?>"<?php selected( $option['is_default'] ); ?>><?php echo esc_html( $option['label'] ); ?></option>
			<?php } ?>
		</select>
	</div>

</div>
