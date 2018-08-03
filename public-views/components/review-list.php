<?php

/**
 * @var string[] $reviews Rendered product reviews
 * @var string   $pagination
 * @var bool     $wrap
 */

?>


<?php if ( $wrap ) { ?>
	<div class="bc-load-items bc-product-review-list-wrapper">

	<?php if ( ! empty( $pagination ) ) { ?>
		<div class="bc-load-items__loader"></div>
	<?php } ?>

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