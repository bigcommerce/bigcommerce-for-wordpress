<?php

/**
 * @var string[] $reviews Rendered product reviews
 * @var string   $pagination
 * @var bool     $wrap
 * @version 1.0.0
 */

?>


<?php if ( $wrap ) { ?>
	<!-- class="bc-load-items" is required -->
	<div class="bc-load-items bc-product-review-list-wrapper">

	<?php if ( ! empty( $pagination ) ) { ?>
		<!-- class="bc-load-items__loader" is required -->
		<div class="bc-load-items__loader"></div>
	<?php } ?>
	<!-- class="bc-load-items-container" and the conditional class "bc-load-items-container--has-pages" are required. -->
	<div class="bc-product-review-list bc-load-items-container <?php echo( ! empty( $pagination ) ? esc_attr( 'bc-load-items-container--has-pages' ) : '' ); ?>">
<?php } ?>

<?php foreach ( $reviews as $review ) { ?>
	<?php echo $review; ?>
<?php } ?>

<?php echo $pagination; ?>

<?php if ( $wrap ) { ?>
	</div>
	</div>
<?php } ?>