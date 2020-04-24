<?php
/**
 * Template for an order shipment
 *
 * @var string  $method
 * @var string  $tracking_number
 * @var string  $provider
 * @var string  $carrier
 * @var object  $address
 * @var array[] $items
 * @version 1.0.0
 */

/** @var string $csz The City/State/ZIP string */
$csz = '';
if ( $address->city && $address->state && $address->zip ) {
	$csz = sprintf( __( '%1$s, %2$s %3$s', 'bigcommerce' ), $address->city, $address->state, $address->zip );
} elseif ( $address->city && $address->state ) {
	$csz = sprintf( __( '%1$s, %2$s', 'bigcommerce' ), $address->city, $address->state );
} elseif ( ( $address->city || $address->state ) && $address->zip ) {
	$csz = sprintf( __( '%1$s %2$s', 'bigcommerce' ), $address->city . $address->state, $address->zip );
} else {
	$csz = $address->city . $address->state . $address->zip; // take whichever one we have
}
?>

<div class="bc-order-shipment">

	<div class="bc-order-shipment__col">
		<?php if ( $carrier || $tracking_number ) { ?>
		<dl class="bc-order-shipment__tracking">
			<?php if ( $carrier ) { ?>
			<div class="bc-order-shipment__tracking-meta">
				<dt class="bc-order-meta__label"><?php echo esc_html__( 'Carrier', 'bigcommerce' ); ?></dt>
				<dd class="bc-order-meta__value"><?php echo esc_html( $carrier ); ?></dd>
			</div>
			<?php } ?>
			<?php if ( $tracking_number ) { ?>
			<div class="bc-order-shipment__tracking-meta">
				<dt class="bc-order-meta__label"><?php echo esc_html__( 'Tracking Number', 'bigcommerce' ); ?></dt>
				<dd class="bc-order-meta__value"><?php echo esc_html( $tracking_number ); ?></dd>
			</div>
			<?php } ?>
		</dl>
		<?php } ?>

		<div class="bc-order-shipment__items">
			<h4 class="bc-order-meta__label"><?php echo esc_html__( 'Products', 'bigcommerce' ); ?></h4>
			<?php foreach ( $items as $item ) { ?>
				<div class="bc-order-shipment__item bc-order-meta__value">
					<?php echo esc_html( sprintf( __( '%d × %s', 'bigcommerce' ), $item[ 'quantity' ], $item[ 'title' ] ) ); ?>
				</div>
			<?php } ?>
		</div>
	</div>

	<div class="bc-order-shipment__col">
		<div class="bc-order-shipment__address">
			<h4 class="bc-order-meta__label"><?php echo esc_html__( 'Ship To', 'bigcommerce' ); ?></h4>
			<div class="bc-order-meta__value">
				<span class="bc-order-shipment__name">
					<?php echo esc_html( sprintf( __( '%1$s %2$s', 'bigcommerce' ), $address->first_name, $address->last_name ) ); ?>
				</span>
				<?php if ( $address->company ) { ?>
					<span class="bc-order-shipment__company">
						<?php echo esc_html( $address->company ); ?>
					</span>
				<?php } ?>
				<?php if ( $address->street_1 ) { ?>
					<span class="bc-order-shipment__street1">
						<?php echo esc_html( $address->street_1 ); ?>
					</span>
				<?php } ?>
				<?php if ( $address->street_2 ) { ?>
					<span class="bc-order-shipment__street2">
						<?php echo esc_html( $address->street_2 ); ?>
					</span>
				<?php } ?>
				<?php if ( $csz ) { ?>
					<span class="bc-order-shipment__city-state-zip">
						<?php echo esc_html( $csz ); ?>
					</span>
				<?php } ?>
				<?php if ( $address->country ) { ?>
					<span class="bc-order-shipment__country">
						<?php echo esc_html( $address->country ); ?>
					</span>
				<?php } ?>
			</div>
		</div>
	</div>

</div>
