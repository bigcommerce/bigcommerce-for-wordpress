<?php
/**
 * Display the text product option field for a product
 *
 * @var string $id
 * @var string $label
 * @var bool   $required
 * @var int    $minlength
 * @var int    $maxlength
 * @var string $default_value
 * @version 1.0.1
 */

?>

<div class="bc-product-form__control bc-product-form__control--text">

	<label for="option-<?php echo esc_attr( $id ); ?>" class="bc-form__label bc-product-form__option-label <?php if ( $required ) { echo 'bc-form-control-required'; } ?>">
		<?php echo esc_html( $label ); ?>
	</label>

	<div class="bc-product-form__option-field">
		<input type="text"
			name="option[<?php echo esc_attr( $id ); ?>]"
			id="option-<?php echo esc_attr( $id ); ?>"
			value="<?php echo esc_attr( $default_value ); ?>"
			class="bc-product-option__text"
			data-js="bc-product-option-field"
			data-option-id="<?php echo esc_attr( $id ); ?>"
			<?php if ( $required ) { echo 'required="required"'; } ?>
			<?php if ( $minlength ) { printf( 'minlength="%d"', absint( $minlength ) ); } ?>
			<?php if ( $maxlength ) { printf( 'maxlength="%d"', absint( $maxlength ) ); } ?>
		>
		<?php if ( $minlength ) { ?>
			<span class="bc-product-form__option-description">
				<?php echo sprintf( __( 'Minimum character count is: <strong>%s</strong>', 'bigcommerce' ), esc_html( $minlength ) ); ?>
			</span>
		<?php } ?>
		<?php if ( $maxlength ) { ?>
			<span class="bc-product-form__option-description">
				<?php echo sprintf( __( 'Maximum character count is: <strong>%s</strong>', 'bigcommerce' ), esc_html( $maxlength ) ); ?>
			</span>
		<?php } ?>
	</div>

</div>
