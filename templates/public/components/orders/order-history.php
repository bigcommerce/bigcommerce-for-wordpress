<?php
/**
 * Template for the order history page/shortcode
 *
 * @var string[] $orders Rendered order objects
 * @var string   $pagination
 * @var bool     $wrap
 * @version 1.0.0
 */

?>

<?php if ( $wrap ) { ?>
<!-- class="bc-load-items" is required -->
	<div class="bc-load-items bc-shortcode-order-list-wrapper">

	<?php if ( ! empty( $pagination ) ) { ?>
		<!-- class="bc-load-items__loader" is required -->
		<div class="bc-load-items__loader"></div>
	<?php } ?>
	<!-- class="bc-load-items-container" and the conditional class "bc-load-items-container--has-pages are required -->
	<ul class="bc-order-list bc-load-items-container <?php echo( ! empty( $pagination ) ? esc_attr( 'bc-load-items-container--has-pages' ) : '' ); ?>">
<?php } ?>

<?php foreach ( $orders as $order ) { ?>
	<li class="bc-order-list__item">
		<?php echo $order; ?>
	</li>
<?php } ?>

<?php echo $pagination; ?>

<?php if ( $wrap ) { ?>
	</ul>
	</div>
<?php } ?>