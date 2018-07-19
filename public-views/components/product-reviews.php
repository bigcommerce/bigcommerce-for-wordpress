<?php

/**
 * @var string   $header   Rendered summary header
 * @var string   $form     Rendered product review form. May be empty if reviewing is disabled.
 * @var string[] $reviews  Rendered product reviews
 */

?>

<section class="bc-single-product__reviews" id="bc-single-product__reviews">
	<?php echo $header; ?>

	<?php echo $form; ?>

	<?php if ( $reviews ) { ?>
		<div class="bc-product-review-list">
			<?php foreach ( $reviews as $review ) { ?>
				<?php echo $review; ?>
			<?php } ?>
		</div>
	<?php } ?>
</section>