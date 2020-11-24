<?php
/**
 * Shipping Zones Dropdown
 *
 * @package BigCommerce
 *
 * @var array $cart
 * @version 1.0.0
 */

?>
<select name="shipping-zones" data-js="bc-shipping-zones" data-shipping-field>
	<option value="0" disabled selected><?php esc_html_e( '-- Choose your location', 'bigcommerce' ); ?></option>
	<?php foreach ( $zones as $zone ) : ?>
		<option value="<?php echo esc_attr( $zone['id'] ); ?>"><?php echo esc_html( $zone['name'] ); ?></option>
	<?php endforeach; ?>
</select>
