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

<div id="option-<?php echo esc_attr( $id ); ?>" class="bc-product-form__control bc-product-form__control--rectangle">
	<span class="bc-product-form__option-label"><?php echo esc_html( $label ); ?></span>

	<div class="bc-product-form__option-variants--inline" data-js="product-form-option" data-field="product-form-option-radio">
		<?php foreach ( $options as $option ) { ?>

			<input type="radio"
			       name="option[<?php echo esc_attr( $id ); ?>]"
			       data-option-id="<?php echo esc_attr( $id ); ?>"
			       id="option--<?php echo esc_attr( $option['id'] ); ?>"
			       value="<?php echo esc_attr( $option['id'] ); ?>"
			       class="u-bc-visual-hide bc-product-variant__radio--hidden"
                   on="change:AMP.setState( { variants<?php echo esc_attr( $post_id ); ?>: { currentOptions: { <?php echo esc_attr( $id ); ?>: event.value } } } )"
				<?php checked( $option['is_default'] ); ?> />

			<label for="option--<?php echo esc_attr( $option['id'] ); ?>" class="bc-product-variant__label">
				<span class="bc-product-variant__label--rectangle">
					<?php echo esc_html( $option['label'] ); ?>
				</span>
			</label>

		<?php } ?>
	</div>
</div>
