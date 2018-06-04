<?php
/**
 * Renders the button to load the next page in a paginated shortcode
 *
 * @var string $next_page_url
 */
?>

<?php if ( ! empty( $next_page_url ) ) { ?>
	<section class="bc-load-items__trigger bc-load-items__trigger--posts" data-js="load-items-trigger">
		<button type="button" class="bc-load-items__trigger-btn bc-load-items__trigger-btn--posts" data-js="load-items-trigger-btn"
		        data-href="<?php echo esc_url( $next_page_url ); ?>">
			<?php echo esc_html( apply_filters( 'bigcommerce/shortcode/load_more_text', __( 'Load More Products', 'bigcomerce' ) ) ); ?>
			<i class="bc-icon icon-bc-chevron-down"></i>
		</button>
	</section>
<?php } ?>
