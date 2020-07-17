<?php
/**
 * Display the textarea (multi_line_text) product option field for a product
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
 * 
 * @version 1.0.0
 */

?>

<label class="bc-form__label bc-product-form__option-label <?php if ( $required ) { echo 'bc-form-control-required'; } ?>" for="option-<?php echo esc_attr( $id ); ?>">
	<?php echo esc_html( $label ); ?>
</label>

<div class="bc-product-form__option-field">
	<textarea
		name="option[<?php echo esc_attr( $id ); ?>]"
		id="option-<?php echo esc_attr( $id ); ?>"
		class="bc-product-option__textarea"
		data-js="bc-product-option-field"
		data-option-id="<?php echo esc_attr( $id ); ?>"
		<?php if ( $required ) { echo 'required="required"'; } ?>
		<?php if ( $minlength ) { printf( 'minlength="%d"', absint( $minlength ) ); } ?>
		<?php if ( $maxlength ) { printf( 'maxlength="%d"', absint( $maxlength ) ); } ?>
		<?php if ( $maxrows ) { printf( 'data-maxrows="%d"', absint( $maxrows ) ); } ?>
	><?php echo esc_textarea( $default_value ); ?></textarea>
</div>

<?php if ( $minlength ) { ?>
	<span class="bc-product-form__option-description">
		<?php echo sprintf( esc_html( __( 'Minimum characters: <strong>%s</strong>', 'bigcommerce' ) ), $minlength ); ?>
	</span>
<?php } ?>

<?php if ( $maxlength ) { ?>
	<span class="bc-product-form__option-description">
		<?php echo sprintf( esc_html( __( 'Maximum characters: <strong>%s</strong>', 'bigcommerce' ) ), $maxlength ); ?>
	</span>
<?php } ?>

<?php if ( $maxrows ) { ?>
	<span class="bc-product-form__option-description">
		<?php echo sprintf( esc_html( __( 'Maximum lines: <strong>%s</strong>', 'bigcommerce' ) ), $maxrows ); ?>
	</span>
<?php } ?>

