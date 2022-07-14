<?php
/**
 * Shipping Methods
 *
 * @package BigCommerce
 *
 * @var array $cart
 * @version 1.0.0
 */

?>
<ul class="bc-shipping-methods">
	<?php foreach ( $methods as $key => $method ) : ?>
		<li class="bc-shipping-method">
			<?php if ( $method['type'] !== \BigCommerce\Rest\Shipping_Controller::SHIPPING_METHOD_TYPE_USPS ) : ?>
			<input
				id="<?php echo esc_attr( "method-{$method['id']}" ); ?>"
				type="radio"
				name="shipping-method"
				class="bc-shipping-method__option"
				data-type="<?php echo esc_attr( $method['type'] ); ?>"
				data-rate-raw="<?php echo esc_attr( $method['rate_raw'] ); ?>"
				data-rate="<?php echo esc_attr( $method['rate'] ); ?>"
				data-cart-subtotal-raw="<?php echo esc_attr( $method['cart_subtotal_raw'] ); ?>"
				data-cart-subtotal="<?php echo esc_attr( $method['cart_subtotal'] ); ?>"
				data-cart-total-raw="<?php echo esc_attr( $method['cart_total_raw'] ); ?>"
				data-cart-total="<?php echo esc_attr( $method['cart_total'] ); ?>"
				data-fixed-surcharge="<?php echo esc_attr( $method['fixed_surcharge'] ); ?>"
				data-shipping-field
				<?php if ( $key === 0 ) {
					echo esc_attr( 'checked' );
				} ?>
			>
			<label for="<?php echo esc_attr( "method-{$method['id']}" ); ?>" class="bc-shipping-method__label">
				<?php
					$price = $method['rate'];
					if ( $method['type'] === 'peritem' ) {
						$price = sprintf( __( '%s/per item', 'bigcommerce' ), $price );
					}
					printf(
						'<span class="bc-shipping-method__name">%s</span> - <span class="bc-shipping-method__price">%s</span>',
						esc_html( $method['name'] ),
						esc_html( $price )
					);
				?>
			</label>
			<?php else : ?>
				<table>
					<tr>
						<td><?php echo __( 'Country', 'bigcommerce' ) ?></td>
						<td>
							<select data-js="bc-calc-country" class="bc-calc-country">
							<?php
							foreach ($countries as $country) {
								$selected = $country->country_iso2 === 'US' ? 'selected="selected"' : '';
								printf( '<option value="%s" %s>%s</option>', $country->country_iso2, $selected, $country->country );
							}
							?>
							</select>
						</td>
					</tr>
					<tr>
						<td>
							<?php echo __( 'State/province code', 'bigcommerce' ) ?>
						</td>
						<td>
							<input type="text" value="" data-js="bc-calc-state" class="bc-calc-state" />
						</td>
					</tr>
					<tr>
						<td>
							<?php echo __( 'Suburb/city', 'bigcommerce' ) ?>
						</td>
						<td>
							<input type="text" value="" data-js="bc-calc-city" class="bc-calc-city" />
						</td>
					</tr>
					<tr>
						<td>
							<?php echo __( 'Zip/postcode', 'bigcommerce' ) ?>
						</td>
						<td>
							<input type="text" value="" data-js="bc-calc-zip" class="bc-calc-zip" />
						</td>
					</tr>
					<tr>
						<td></td>
						<td>
							<button type="button" data-js="bc-calc-run" class="bc-calc-run">
								<?php echo __( 'Estimate Shipping', 'bigcommerce' ) ?>
							</button>
						</td>
					</tr>
				</table>
			<?php endif; ?>
		</li>
	<?php endforeach; ?>
</ul>

<button class="bc-btn bc-btn--small bc-shipping-calculator-update" data-js="shipping-calculator-update" data-shipping-field type="button"><?php esc_html_e( 'Update Total', 'bigcommerce' ); ?></button>
