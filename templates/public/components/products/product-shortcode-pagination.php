<?php
/**
 * Renders the button to load the next page in a paginated shortcode
 *
 * @package BigCommerce
 *
 * @var string $next_page_url
 * @version 1.0.0
 */

?>

<?php if ( ! empty( $next_page_url ) ) { ?>
	<!-- data-js="load-items-trigger-btn" is required -->
	<button
			type="button"
			class="bc-load-items__trigger-btn bc-load-items__trigger-btn--posts"
			data-js="load-items-trigger-btn"
			data-href="<?php echo esc_url( $next_page_url ); ?>"
	>
		<?php echo esc_html( apply_filters( 'bigcommerce/shortcode/load_more_text', __( 'Load More Products', 'bigcommerce' ) ) ); ?>
		<i class="bc-icon icon-bc-chevron-down"></i>
	</button>
<?php } ?>
