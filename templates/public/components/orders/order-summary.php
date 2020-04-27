<?php
/**
 * Template for a single order on the order history page
 *
 * @var int    $order_id
 * @var string $description
 * @var string $shipping
 * @var string $tax
 * @var string $subtotal
 * @var string $total
 * @var int    $item_count
 * @var string $payment_method
 * @var string $store_credit
 * @var string $discount_amount
 * @var string $coupon_amount
 * @var string $gift_certificate
 * @var string $created_date
 * @var string $updated_date
 * @var int    $image_id
 * @var string $image
 * @var string $status
 * @var string $details_url
 * @var string $support_email
 * @version 1.0.0
 */

?>

<div class="bc-order-card">
	<div class="bc-order__header">
		<div class="bc-order__id">
			<?php echo esc_html( sprintf( __( 'Order #%d', 'bigcommerce' ), $order_id ) ); ?>
		</div>
		<div class="bc-order__link">
			<a href="<?php echo esc_url( $details_url ); ?>"><?php esc_html_e( 'Order Details', 'bigcommerce' ); ?></a>
		</div>
	</div>

	<div class="bc-order-card__body">
		<div class="bc-order-card__featured-image">
			<a class="bc-order-card__featured-image-link"
				 href="<?php echo esc_url( $details_url ); ?>"><?php echo $image; ?></a>
		</div>

		<div class="bc-order-card__meta">
			<div class="bc-order-card__meta-inner">

				<div class="bc-order-card__title">
					<h3 class="bc-order-card__order-title">
						<a class="bc-order-card__title-link"
							 href="<?php echo esc_url( $details_url ); ?>"><?php echo $description; ?></a>
					</h3>
				</div>

				<div class="bc-order-card__total"><?php echo esc_html( $total ); ?></div>

				<dl class="bc-order-card-meta__list">
					<div class="bc-order-card-meta__list-item">
						<dt class="bc-order-meta__label"><?php esc_html_e( 'Ordered', 'bigcommerce' ); ?></dt>
						<dd class="bc-order-meta__value"><?php echo esc_html( $created_date ); ?></dd>
					</div>
					<div class="bc-order-card-meta__list-item">
						<dt class="bc-order-meta__label"><?php esc_html_e( 'Last Update', 'bigcommerce' ); ?></dt>
						<dd class="bc-order-meta__value"><?php echo esc_html( $updated_date ); ?></dd>
					</div>
				</dl>

			</div>
		</div>

		<div class="bc-order-card__status"><?php echo esc_html( $status ); ?></div>
	</div>
</div>
