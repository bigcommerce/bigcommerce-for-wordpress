<?php
/**
 * Display the textarea (multi_line_text) product modifier field for a product
 *
 * @var string $id
 * @var string $label
 * @var bool   $required
 * @var int    $minlength
 * @var int    $maxlength
 * @var int    $maxrows
 * @var string $default_value
 *
 * @todo: client-side validation for max rows
 */

?>

<div class="bc-product-form__control bc-product-form__control--textarea">

	<label class="bc-form__label bc-product-form__modifier-label <?php if ( $required ) { echo 'bc-form-control-required'; } ?>" for="modifier-<?php echo esc_attr( $id ); ?>">
		<?php echo esc_html( $label ); ?>
	</label>

	<div class="bc-product-form__modifier-field">
		<textarea name="modifier[<?php echo esc_attr( $id ); ?>]"
		       id="modifier-<?php echo esc_attr( $id ); ?>"
		       class="bc-product-modifier__textarea"
		       data-modifier-id="<?php echo esc_attr( $id ); ?>"
		       <?php if ( $required ) { echo 'required="required"'; } ?>
		       <?php if ( $minlength ) { printf( 'minlength="%d"', absint( $minlength ) ); } ?>
		       <?php if ( $maxlength ) { printf( 'maxlength="%d"', absint( $maxlength ) ); } ?>
		       <?php if ( $maxrows ) { printf( 'data-maxrows="%d"', absint( $maxrows ) ); } ?>
		><?php echo esc_textarea( $default_value ); ?></textarea>
	</div>

	<?php if ( $minlength ) { ?>
		<span class="bc-product-form__modifier-description">
			<?php echo sprintf( __( 'Minimum characters: <strong>%s</strong>', 'bigcommerce' ), $minlength ); ?>
		</span>
	<?php } ?>

	<?php if ( $maxlength ) { ?>
		<span class="bc-product-form__modifier-description">
			<?php echo sprintf( __( 'Maximum characters: <strong>%s</strong>', 'bigcommerce' ), $maxlength ); ?>
		</span>
	<?php } ?>

	<?php if ( $maxrows ) { ?>
		<span class="bc-product-form__modifier-description">
			<?php echo sprintf( __( 'Maximum lines: <strong>%s</strong>', 'bigcommerce' ), $maxrows ); ?>
		</span>
	<?php } ?>

</div>
