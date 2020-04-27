<?php
/**
 * Display the fields to select options for a product
 *
 * @var string  $id
 * @var string  $label
 * @var array[] $options
 * @version 1.0.0
 */

?>

<div for="option-<?php echo intval( $id, 10 ); ?>" class="bc-product-form__control bc-product-form__control--pick-list">
	<span class="bc-product-form__option-label"><?php echo esc_html( $label ); ?></span>

	<div class="bc-product-form__option-variants" data-js="product-form-option" data-field="product-form-option-radio">
		<?php foreach ( $options as $option ) { ?>

			<input type="radio"
				   name="option[<?php echo intval( $id, 10 ); ?>]"
				   data-option-id="<?php echo intval( $id, 10 ); ?>"
				   id="option--<?php echo intval( $option['id'], 10 ); ?>"
				   value="<?php echo intval( $option['id'], 10 ); ?>"
				   class="u-bc-visual-hide bc-product-variant__radio--hidden"
				   on="change:AMP.setState( { variants<?php echo intval( $post_id, 10 ); ?>: { currentOptions: { <?php echo intval( $option['id'], 10 ); ?>: event.value } } } )"

				<?php checked( $option['is_default'] ); ?> />

			<label for="option--<?php echo intval( $option['id'], 10 ); ?>" class="bc-product-variant__label">
			<span class="bc-product-variant__label--pick-list">
				<?php
				if ( $option['attachment_id'] ) {
					echo wp_kses( wp_get_attachment_image( $option['attachment_id'], 'bc-thumb', false, [ 'class' => 'bc-product-variant__label--img' ] ), 'bigcommerce/amp' );
				}
				?>
				<span class="bc-product-variant__label--title">
					<?php echo esc_html( $option['label'] ); ?>
				</span>
			</span>
			</label>

		<?php } ?>
	</div>

</div>
