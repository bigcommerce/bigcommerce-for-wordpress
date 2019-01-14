<?php
/**
 * Display the text product modifier field for a product
 *
 * @var string $id
 * @var string $label
 * @var bool   $required
 * @var int    $minlength
 * @var int    $maxlength
 * @var string $default_value
 */

?>

<div class="bc-product-form__control bc-product-form__control--text">

	<label class="bc-form__label bc-product-form__modifier-label <?php if ( $required ) { echo 'bc-form-control-required'; } ?>" for="modifier-<?php echo esc_attr( $id ); ?>">
		<?php echo esc_html( $label ); ?>
	</label>

	<div class="bc-product-form__modifier-field">
		<input type="text" name="modifier[<?php echo esc_attr( $id ); ?>]"
		       id="modifier-<?php echo esc_attr( $id ); ?>"
		       class="bc-product-modifier__text"
		       data-js="bc-product-modifier-field"
		       data-modifier-id="<?php echo esc_attr( $id ); ?>"
		       <?php if ( $required ) { echo 'required="required"'; } ?>
		       <?php if ( $minlength ) { printf( 'minlength="%d"', absint( $minlength ) ); } ?>
		       <?php if ( $maxlength ) { printf( 'maxlength="%d"', absint( $maxlength ) ); } ?>
		       value="<?php echo esc_attr( $default_value ); ?>"
		/>

		<?php if ( $minlength ) { ?>
			<span class="bc-product-form__modifier-description">
				<?php echo sprintf( __( 'Minimum character count is: <strong>%s</strong>', 'bigcommerce' ), $minlength ); ?>
			</span>
		<?php } ?>

		<?php if ( $maxlength ) { ?>
			<span class="bc-product-form__modifier-description">
				<?php echo sprintf( __( 'Maximum character count is: <strong>%s</strong>', 'bigcommerce' ), $maxlength ); ?>
			</span>
		<?php } ?>
	</div>

</div>
