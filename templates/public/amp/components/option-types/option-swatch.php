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

<div id="option-<?php echo esc_attr( $id ); ?>" class="bc-product-form__control bc-product-form__control--swatch">
	<span class="bc-product-form__option-label"><?php echo esc_html( $label ); ?></span>

	<div class="bc-product-form__option-variants bc-product-form__option-variants--inline" data-js="product-form-option" data-field="product-form-option-radio">
		<?php foreach ( $options as $option ) { ?>

			<input type="radio"
			       name="option[<?php echo esc_attr( $id ); ?>]"
			       data-option-id="<?php echo esc_attr( $id ); ?>"
			       id="option--<?php echo esc_attr( $option['id'] ); ?>"
			       value="<?php echo esc_attr( $option['id'] ); ?>"
			       class="u-bc-visual-hide bc-product-variant__radio--hidden"
                   on="change:AMP.setState( { variants<?php echo esc_attr( $post_id ); ?>: { currentOptions: { <?php echo esc_attr( $id ); ?>: event.value } } } )"
				<?php checked( $option['is_default'] ); ?> />

			<label for="option--<?php echo esc_attr( $option[ 'id' ] ); ?>" class="bc-product-variant__label">
				<?php if ( $option[ 'type' ] == 'image' ) { ?>
					<span class="bc-product-variant__label--swatch bc-product-variant__label--image"
					      style="background-image: url(<?php echo esc_url( $option[ 'src' ] ); ?>);">
					</span>
				<?php } elseif ( $option[ 'type' ] == '3-color' ) {
					$gradient = sprintf( '45deg, %1$s 0%%, %1$s 34%%, %2$s 34%%, %2$s 66%%, %3$s 66%%, %3$s 100%%', esc_attr( $option[ 'colors' ][ 0 ] ), esc_attr( $option[ 'colors' ][ 1 ] ), esc_attr( $option[ 'colors' ][ 2 ] ) );
					?>
					<span class="bc-product-variant__label--swatch bc-product-variant__label--3-color"
					      style="background: linear-gradient(<?php echo $gradient; // WPCS: XSS ok. Already escaped data. ?>)">
					</span>
				<?php } elseif ( $option[ 'type' ] == '2-color' ) {
					$gradient = sprintf( '45deg, %1$s 0%%, %1$s 50%%, %2$s 50%%, %2$s 100%%', esc_attr( reset( $option[ 'colors' ] ) ), esc_attr( end( $option[ 'colors' ] ) ) );
					?>
					<span class="bc-product-variant__label--swatch bc-product-variant__label--2-color"
					      style="background: linear-gradient(<?php echo $gradient; // WPCS: XSS ok. Already escaped data. ?>)">
					</span>
				<?php } elseif ( $option[ 'type' ] == '1-color' ) { ?>
					<span class="bc-product-variant__label--swatch bc-product-variant__label--1-color"
					      style="background-color: <?php echo esc_attr( reset( $option[ 'colors' ] ) ); ?>;">
					</span>
				<?php } ?>
				<span class="u-bc-visual-hide"><?php echo esc_html( $option[ 'label' ] ); ?></span>
			</label>

		<?php } ?>
	</div>
</div>
