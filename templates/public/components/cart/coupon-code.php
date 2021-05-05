<?php
/**
 * Coupon Code
 *
 * @package BigCommerce
 *
 * @var array $coupons
 * @version 1.0.0
 */

?>
<!-- data-js="bc-add-coupon-form" is required -->
<div class="bc-add-coupon-form" data-js="bc-add-coupon-form" aria-hidden="<?php empty( $coupons ) ? esc_attr_e( 'false' ) : esc_attr_e( 'true' ); ?>">
	<!-- data-js="bc-coupon-code-field" is required -->
	<input
			id="coupon_code"
			type="text"
			name="coupon_code"
			class="bc-coupon-code-field"
			data-js="bc-coupon-code-field"
			placeholder="<?php echo esc_attr( 'Enter Coupon Code', 'bigcommerce' ); ?>"
	>
	<!-- data-js="bc-coupon-code-submit" is required -->
	<button
			class="bc-btn bc-btn--small bc-coupon-code-submit"
			data-js="bc-coupon-code-submit"
			type="button"
	>
		<?php esc_html_e( 'Apply Coupon', 'bigcommerce' ); ?>
	</button>
</div>

<!-- data-js="bc-remove-coupon-form" is required -->
<div class="bc-remove-coupon-form" data-js="bc-remove-coupon-form" aria-hidden="<?php empty( $coupons ) ? esc_attr_e( 'true' ) : esc_attr_e( 'false' ); ?>">
	<!-- data-js="bc-coupon-code-submit" is required -->
	<button
			type="button"
			aria-label="<?php esc_html_e( 'Remove Coupon', 'bigcommerce' ); ?>"
			class="bc-btn bc-btn--small bc-coupon-code-remove"
			data-js="bc-coupon-code-remove"
			data-coupon-code="<?php ! empty( $coupons ) ? esc_attr_e( $coupons[0]['code'] ) : ''; ?>"
	>
		<?php printf( '<i class="bc-icon icon-bc-cross"></i> <span class="bc-coupon-name">%s</span>', ! empty( $coupons ) ? esc_html( $coupons[0]['code'] ) : '' ); ?>
	</button>

	<!-- data-js="bc-coupon-details" is required -->
	<?php
		printf( '<span class="bc-coupon-details" data-js="bc-coupon-details">%s: -%s</span>',
			__( 'Discount', 'bigcommerce' ),
			! empty( $coupons ) ? esc_html( $coupons[0]['discounted_amount']['formatted'] ) : ''
		);
	?>
</div>
