<?php
/**
 * Renders the button to load the next page of product reviews
 *
 * @var string $next_page_url
 * @var string $first_page_url
 * @version 1.1.0
 */
?>

<?php if ( ! empty( $next_page_url ) && empty( $first_page_url ) ) { ?>
	<!-- data-js="data-js="load-items-trigger-btn" is required -->
	<button
			type="button"
			class="bc-load-items__trigger-btn bc-load-items__trigger-btn--reviews"
			data-js="load-items-trigger-btn"
			data-href="<?php echo esc_url( $next_page_url ); ?>"
	>
		<?php echo esc_html( apply_filters( 'bigcommerce/product/reviews/load_more_text', __( 'Load More', 'bigcommerce' ) ) ); ?>
		<i class="bc-icon icon-bc-chevron-down"></i>
	</button>
<?php } ?>
