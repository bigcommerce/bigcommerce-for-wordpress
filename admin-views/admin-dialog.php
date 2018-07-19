<?php
/**
 * Admin UI Dialog Template.
 *
 * @package BigCommerce Admin
 *
 * @var string $query_builder_sidebar
 * @var string $query_settings_sidebar
 */

?>

<script data-js="bc-shortcode-ui" type="text/template">
	<section class="bc-shortcode-ui" data-js="bc-shortcode-ui-container">

		<?php echo $query_builder_sidebar; ?>

		<div class="bc-shortcode-ui__products-wrapper">

			<section class="bc-shortcode-ui__products" data-js="bc-shortcode-ui-products">
				<div class="bc-shortcode-ui__search">
					<form
							action="<?php echo esc_url( home_url() ); ?>"
							method="get"
							enctype="multipart/form-data"
							data-js="bc-shortcode-ui-search"
							class="bc-shortcode-ui__searchform"
					>
						<label
								for="bcqb-input"
								class="screen-reader-text"
						><?php esc_html_e( 'Search for products', 'bigcommerce' ); ?></label>
						<input
								id="bcqb-input"
								type="text"
								placeholder="<?php esc_attr_e( 'Product ID, Name, SKU', 'bigcommerce' ); ?>"
								class="bc-shortcode-ui__search-input"
								data-js="bcqb-input"
						>
						<button
								type="button"
								class="bc-shortcode-ui-search__submit-button"
								data-js="bcqb-submit"
						><?php esc_html_e( 'Search', 'bigcommerce' ); ?></button>
						<button
								type="button"
								class="bc-shortcode-ui-search__clear-search"
								data-js="bcqb-clear"
						><?php esc_html_e( '(Clear Search)', 'bigcommerce' ); ?> </button>
					</form>
				</div>

				<div class="bc-shortcode-ui__product-grid" data-js="bc-shortcode-ui-query-results"></div>

				<div class="bc-shortcode-ui__product-query-dimmer"></div>

				<div class="bc-shortcode-ui__product-query-loader"></div>
			</section>

			<?php echo $query_settings_sidebar; ?>

		</div>

	</section>
</script>
