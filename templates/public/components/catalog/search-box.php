<?php
/**
 * The template for rendering the product search form
 *
 * @var string $name   The field name
 * @var string $value  The default value of the field
 * @var string $action The form action URL
 * @var string $placeholder Placeholder text string for text input.
 * @var string $search_label The text string for the label attribute and button.
 * @var string $button_classes The text string of classes for the submit button.
 * @version 1.0.0
 */
?>
<div class="bc-product-archive__search">
	<label for="<?php echo esc_attr( $name ); ?>" class="u-bc-visual-hide"><?php esc_html_e( 'Search', 'bigcommerce' ); ?></label>
	<input type="search" name="<?php echo esc_attr( $name ); ?>" value="<?php echo esc_attr( $value ); ?>" placeholder="<?php echo esc_attr( $placeholder ); ?>"/>
	<button type="submit" class="<?php echo esc_attr( $button_classes ); ?>" aria-label="<?php esc_html_e( 'Search Submit', 'bigcommerce' ); ?>">
		<span class="u-bc-visual-hide"><?php esc_html_e( 'Search', 'bigcommerce' ); ?></span>
	</button>
</div>
