<?php
/**
 * Single Address Template
 *
 * @var int    $id
 * @var string $first_name
 * @var string $last_name
 * @var string $company
 * @var string $street_1
 * @var string $street_2
 * @var string $city
 * @var string $state
 * @var string $zip
 * @var string $country
 * @var string $phone
 * @version 1.0.0
 */

/** @var string $csz The City/State/ZIP string */
$csz = '';
if ( $city && $state && $zip ) {
	$csz = sprintf( __( '%1$s, %2$s %3$s', 'bigcommerce' ), $city, $state, $zip );
} elseif ( $city && $state ) {
	$csz = sprintf( __( '%1$s, %2$s', 'bigcommerce' ), $city, $state );
} elseif ( ( $city || $state ) && $zip ) {
	$csz = sprintf( __( '%1$s %2$s', 'bigcommerce' ), $city . $state, $zip );
} else {
	$csz = $city . $state . $zip; // take whichever one we have
}

?>
<div class="bc-account-address__meta">
	<span class="bc-account-address__name">
		<?php echo esc_html( sprintf( __( '%1$s %2$s', 'bigcommerce' ), $first_name, $last_name ) ); ?>
	</span>
	<?php if ( $company ) { ?>
		<span class="bc-account-address__company">
			<?php echo esc_html( $company ); ?>
		</span>
	<?php } ?>
	<?php if ( $street_1 ) { ?>
		<span class="bc-account-address__street1">
			<?php echo esc_html( $street_1 ); ?>
		</span>
	<?php } ?>
	<?php if ( $street_2 ) { ?>
		<span class="bc-account-address__street2">
			<?php echo esc_html( $street_2 ); ?>
		</span>
	<?php } ?>
	<?php if ( $csz ) { ?>
		<span class="bc-account-address__city-state-zip">
			<?php echo esc_html( $csz ); ?>
		</span>
	<?php } ?>
	<?php if ( $country ) { ?>
		<span class="bc-account-address__country">
			<?php echo esc_html( $country ); ?>
		</span>
	<?php } ?>
	<?php if ( $phone ) { ?>
		<span class="bc-account-address__phone">
			<?php echo esc_html( sprintf( __( 'Phone: %s', 'bigcommerce' ), $phone ) ); ?>
		</span>
	<?php } ?>
</div>
