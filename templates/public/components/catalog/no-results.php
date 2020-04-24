<?php
/**
 * @var Product $product
 * @var string  $product_archive_permalink
 * @var string  $no_results_message
 * @var string  $reset_button_label
 * @version 1.0.0
 */

use BigCommerce\Post_Types\Product\Product;

?>

<div class="bc-no-results">
	<p class="bc-no-results__message"><?php echo esc_html( $no_results_message ); ?></p>
	<!-- data-js="bc-reset-filters" is required -->
	<a href="<?php echo esc_url( $product_archive_permalink ); ?>" class="bc-no-results__button bc-btn--reset-filters" data-js="bc-reset-filters">
		<?php echo esc_html( $reset_button_label ); ?>
	</a>
</div>
