<?php
/**
 * Renders the gift certificate balance.
 *
 * @var string $balance The balance of the submitted gift certificate. Already formatted as currencty.
 * @var string $code    The code of the submitted gift certificate.
 * @var string $message Error/message to display to the user.
 * @version 1.0.0
 */
?>
<div class="bc-gift-balance__response">
	<?php if ( $message ) { ?>
		<div class="bc-alert bc-alert--error">
			<?php echo esc_html( $message ); ?>
		</div>
	<?php } ?>
	<div class="bc-gift-balance__container">
		<div class="bc-gift-balance__title"><?php echo esc_html( __( 'Balance Remaining', 'bigcommerce' ) ); ?></div>
		<span class="bc-gift-balance__amount"><?php echo esc_html( $balance ); ?></span>
		<span class="bc-gift-balance__code"><?php echo esc_html( $code ); ?></span>
	</div>
</div>
