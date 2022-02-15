<?php
/**
 * Display the number product option field for a product
 *
 * @var string $id
 * @var string $label
 * @var bool   $required
 * @var int    $min_value
 * @var int    $max_value
 * @var string $step
 * @var string $default_value
 * @version 1.0.1
 */

?>

<div class="bc-product-form__control bc-product-form__control--number">

	<label for="option-<?php echo esc_attr( $id ); ?>" class="bc-form__label bc-product-form__option-label <?php if ( $required ) { echo 'bc-form-control-required'; } ?>">
		<?php echo esc_html( $label ); ?>
	</label>

	<div class="bc-product-form__option-field">
		<input type="number"
			name="option[<?php echo esc_attr( $id ); ?>]"
			id="option-<?php echo esc_attr( $id ); ?>"
			value="<?php echo esc_attr( $default_value ); ?>"
			class="bc-product-option__number"
			data-js="bc-product-option-field"
			data-option-id="<?php echo esc_attr( $id ); ?>"
			step="<?php echo esc_attr( $step ); ?>"
			<?php if ( $required ) { echo 'required="required"'; } ?>
			<?php if ( $min_value !== null ) { printf( 'min="%s"', esc_attr( $min_value ) ); } ?>
			<?php if ( $max_value !== null ) { printf( 'max="%s"', esc_attr( $max_value ) ); } ?>
		>
	</div>

	<?php if ( $min_value && $max_value ) { ?>
		<span class="bc-product-form__option-description">
			<?php echo sprintf( __( 'Minimum value: <strong>%s</strong>, Maximum value: <strong>%s</strong>', 'bigcommerce' ), esc_html( $min_value ), esc_html( $max_value ) ); ?>
		</span>
	<?php } elseif ( $min_value ) { ?>
		<span class="bc-product-form__option-description">
			<?php echo sprintf( __( 'Minimum value: <strong>%s</strong>', 'bigcommerce' ), esc_html( $min_value ) ); ?>
		</span>
	<?php } elseif ( $max_value ) { ?>
		<span class="bc-product-form__option-description">
			<?php echo sprintf( __( 'Maximum value: <strong>%s</strong>', 'bigcommerce' ), esc_html( $max_value ) ); ?>
		</span>
	<?php } ?>

	<?php if ( ! empty( $step ) && 1 === intval( $step ) ) { ?>
		<span class="bc-product-form__option-description">
			<?php echo esc_html( __( 'Whole numbers only', 'bigcommerce' ) ); ?>
		</span>
	<?php } ?>

</div>
