<?php
/**
 * The template for rendering the product sort dropdown
 *
 * @var string   $label   The field label
 * @var string   $name    The field name
 * @var string   $value   The default value of the field
 * @var string   $action  The form action URL
 * @var string[] $choices The choices for the select options
 * @var string   $type    Type of select box control (filter or sort)
 * @version 1.0.0
 */
?>
<div class="bc-product-archive__select bc-product-archive--<?php echo esc_attr( $type ); ?>">
	<label for="<?php echo esc_attr( $name ); ?>" class="bc-product-archive__select-label">
		<?php echo esc_html( $label ); ?>
	</label>
	<select name="<?php echo esc_attr( $name ); ?>" id="<?php echo esc_attr( $name ); ?>" on="change:bc-search-refinery.submit" class="bc-product-archive__select-field">
		<?php foreach ( $choices as $key => $option_label ) { ?>
			<option value="<?php echo esc_attr( $key ); ?>" <?php selected( $key, $value ); ?>><?php echo esc_html( $option_label ); ?></option>
		<?php } ?>
	</select>
</div>
