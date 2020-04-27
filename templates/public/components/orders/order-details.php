<?php
/**
 * Template for a single order's details
 *
 * @var int      $order_id
 * @var string[] $products The fully rendered line items
 * @var string[] $shipments The fully rendered shipments
 * @var string   $description
 * @var string   $shipping
 * @var string   $tax
 * @var string   $subtotal
 * @var string   $total_ex_tax
 * @var string   $total
 * @var int      $item_count
 * @var string   $payment_method
 * @var string   $store_credit
 * @var string   $discount_amount
 * @var string   $coupon_amount
 * @var string   $gift_certificate
 * @var string   $created_date
 * @var string   $updated_date
 * @var int      $image_id
 * @var string   $image
 * @var string   $status
 * @var string   $details_url
 * @var string   $support_email
 * @version 1.0.0
 */

?>

<div class="bc-order-detail">
	<div class="bc-order__header">
		<div class="bc-order__id">
			<?php echo esc_html( sprintf( __( 'Order #%d', 'bigcommerce' ), $order_id ) ); ?>
		</div>
	</div>

	<ul class="bc-order__product-list">
	<?php foreach ( $products as $product ) { ?>
		<li class="bc-order__product-list-item">
			<?php echo $product; ?>
		</li>
	<?php } ?>
	</ul>

	<div class="bc-order-detail__body">

		<div class="bc-order-detail__col bc-order-detail__meta bc-order-detail-meta">
			<dl class="bc-order-meta__list bc-order-detail-meta__list">
				<div class="bc-order-meta__list-item bc-order-detail-meta__list-item">
					<dt class="bc-order-meta__label"><?php esc_html_e( 'Ordered', 'bigcommerce' ); ?></dt>
					<dd class="bc-order-meta__value"><?php echo esc_html( $created_date ); ?></dd>
				</div>
				<div class="bc-order-meta__list-item bc-order-detail-meta__list-item">
					<dt class="bc-order-meta__label"><?php esc_html_e( 'Last Update', 'bigcommerce' ); ?></dt>
					<dd class="bc-order-meta__value"><?php echo esc_html( $updated_date ); ?></dd>
				</div>
				<div class="bc-order-meta__list-item bc-order-detail-meta__list-item">
					<dt class="bc-order-meta__label"><?php esc_html_e( 'Order Status', 'bigcommerce' ); ?></dt>
					<dd class="bc-order-meta__value"><?php echo esc_html( $status ); ?></dd>
				</div>

				<?php if ( $shipped_date ) { ?>
				<div class="bc-order-meta__list-item bc-order-detail-meta__list-item">
					<dt class="bc-order-meta__label"><?php esc_html_e( 'Shipped Date', 'bigcommerce' ); ?></dt>
					<dd class="bc-order-meta__value"><?php echo esc_html( $shipped_date ); ?></dd>
				</div>
				<?php } ?>
				<div class="bc-order-meta__list-item bc-order-detail-meta__list-item">
					<dt class="bc-order-meta__label"><?php esc_html_e( 'Payment Method', 'bigcommerce' ); ?></dt>
					<dd class="bc-order-meta__value"><?php echo esc_html( $payment_method ); ?></dd>
				</div>
			</dl>

			<?php if ( $shipments ) { ?>
			<div class="bc-order-detail__shipments">
				<?php foreach ( $shipments as $shipment ) { ?>
					<?php echo $shipment; ?>
				<?php } ?>
			</div>
			<?php } ?>
		</div>

		<div class="bc-order-detail__col bc-order-detail__totals bc-order-detail-totals">
			<dl class="bc-order-detail-totals__list">
				<div class="bc-order-detail-totals__list-item">
					<dt class="bc-order-detail-totals__label"><?php echo esc_html( _n( 'Item Subtotal', 'Items Subtotal', $item_count, 'bigcommerce' ) ); ?>: </dt>
					<dd class="bc-order-detail-totals__value"><?php echo esc_html( $subtotal ); ?></dd>
				</div>
				<?php if ( $shipping ) { ?>
					<div class="bc-order-detail-totals__list-item">
						<dt class="bc-order-detail-totals__label"><?php esc_html_e( 'Shipping & Handling', 'bigcommerce' ); ?>: </dt>
						<dd class="bc-order-detail-totals__value"><?php echo esc_html( $shipping ); ?></dd>
					</div>
				<?php } ?>
				<?php if ( $discount_amount ) { ?>
					<div class="bc-order-detail-totals__list-item">
						<dt class="bc-order-detail-totals__label"><?php esc_html_e( 'Promotion Applied', 'bigcommerce' ); ?>: </dt>
						<dd class="bc-order-detail-totals__value">-<?php echo esc_html( $discount_amount ); ?></dd>
					</div>
				<?php } ?>
				<?php if ( $coupon_amount ) { ?>
					<div class="bc-order-detail-totals__list-item">
						<dt class="bc-order-detail-totals__label"><?php esc_html_e( 'Your Coupon Savings', 'bigcommerce' ); ?>: </dt>
						<dd class="bc-order-detail-totals__value">-<?php echo esc_html( $coupon_amount ); ?></dd>
					</div>
				<?php } ?>
				<?php if ( $store_credit ) { ?>
					<div class="bc-order-detail-totals__list-item">
						<dt class="bc-order-detail-totals__label"><?php esc_html_e( 'Store Credit Applied', 'bigcommerce' ); ?>: </dt>
						<dd class="bc-order-detail-totals__value">-<?php echo esc_html( $store_credit ); ?></dd>
					</div>
				<?php } ?>
				<?php if ( $gift_certificate ) { ?>
					<div class="bc-order-detail-totals__list-item">
						<dt class="bc-order-detail-totals__label"><?php esc_html_e( 'Gift Certificate', 'bigcommerce' ); ?>: </dt>
						<dd class="bc-order-detail-totals__value">-<?php echo esc_html( $gift_certificate ); ?></dd>
					</div>
				<?php } ?>
				<?php if( $total_ex_tax ){ ?>
				<div class="bc-order-detail-totals__list-item">
					<dt class="bc-order-detail-totals__label"><?php esc_html_e( 'Total Before Tax', 'bigcommerce' ); ?>: </dt>
					<dd class="bc-order-detail-totals__value"><?php echo esc_html( $total_ex_tax ); ?></dd>
				</div>
				<?php } ?>
				<?php if ( $tax ) { ?>
				<div class="bc-order-detail-totals__list-item">
					<dt class="bc-order-detail-totals__label"><?php esc_html_e( 'Estimated tax to be collected', 'bigcommerce' ); ?>: </dt>
					<dd class="bc-order-detail-totals__value"><?php echo esc_html( $tax ); ?></dd>
				</div>
				<?php } ?>
				<div class="bc-order-detail-totals__list-item">
					<dt class="bc-order-detail-totals__label bc-order-detail-totals__label--lg"><?php esc_html_e( 'Grand Total', 'bigcommerce' ); ?>: </dt>
					<dd class="bc-order-detail-totals__value bc-order-detail-totals__value--lg"><?php echo esc_html( $total ); ?></dd>
				</div>
			</dl>
		</div>

	</div>

	<?php if ( $support_email ) { ?>
		<p><?php esc_html_e( "Having problems? Send us an email.", 'bigcommerce' ); ?>
			<a href="mailto:<?php echo esc_attr( $support_email ); ?>"><?php echo esc_html( $support_email ); ?></a>
		</p>
	<?php } ?>

</div>
