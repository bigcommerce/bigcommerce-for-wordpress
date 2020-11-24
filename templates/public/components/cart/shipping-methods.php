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
        </li>
    <?php endforeach; ?>
</ul>

<button class="bc-btn bc-btn--small bc-shipping-calculator-update" data-js="shipping-calculator-update" data-shipping-field type="button"><?php esc_html_e( 'Update Total', 'bigcommerce' ); ?></button>
