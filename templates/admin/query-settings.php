<?php
/**
 * Admin UI Query Settings Template.
 *
 * @package BigCommerce Admin
 *
 * @var string $posts_per_page
 */

?>

<aside
		class="bc-shortcode-ui__settings"
		data-js="bc-shortcode-ui-settings"
		role="complementary"
>

	<section class="bc-shortcode-ui__settings-header" data-js="bc-shortcode-ui-settings-header">
		<div class="bc-shortcode-ui__default-header active">
			<h4 class="bc-shortcode-ui__settings-title"><?php echo esc_html_x( 'Embed BigCommerce Products', 'default settings sidebar header', 'bigcommerce' ); ?></h4>
			<p><?php echo esc_html_x( 'Customize your embedded products by selecting brands, categories or individual products.', 'description for dynamic listing terms sidebar', 'bigcommerce' ); ?></p>
		</div>

		<div class="bc-shortcode-ui__dynamic-listing-header">
			<h4 class="bc-shortcode-ui__settings-title"><?php echo esc_html_x( 'Dynamic Listing', 'header for dynamic listing terms sidebar', 'bigcommerce' ); ?></h4>
			<p><?php echo esc_html_x( 'Create a product loop with your current search terms.', 'description for dynamic listing terms sidebar', 'bigcommerce' ); ?></p>
		</div>

		<div class="bc-shortcode-ui__manual-listing-header">
			<h4 class="bc-shortcode-ui__settings-title"><?php echo esc_html_x( 'Manual Selection', 'header for selected products sidebar', 'bigcommerce' ); ?></h4>
			<p><?php echo esc_html_x( 'Manually embed products from your BigCommerce inventory.', 'description for dynamic listing terms sidebar', 'bigcommerce' ); ?></p>
		</div>
	</section>

	<section class="bc-shortcode-ui__selections" data-js="bc-shortcode-ui-selections">
		<div class="bc-shortcode-ui__selected-terms" data-js="bc-shortcode-ui-selected-terms">
			<ul class="bc-shortcode-ui__terms-list" data-js="bc-shortcode-ui-terms-list"></ul>
			<div class="bc-shortcode-ui__overflow-mask"></div>
		</div>

		<div class="bc-shortcode-ui__selected-products" data-js="bc-shortcode-ui-selected-products">
			<ul class="bc-shortcode-ui__products-list" data-js="bc-shortcode-ui-product-list"></ul>
			<div class="bc-shortcode-ui__overflow-mask"></div>
		</div>
	</section>

	<section class="bc-shortcode-ui__actions" data-js="bc-shortcode-ui-display-settings">
		<h4 class="bc-shortcode-ui__settings-title"><?php echo esc_html_x( 'Display Settings', 'header for display settings sidebar', 'bigcommerce' ); ?></h4>
		<div class="bc-shortcode-ui__display-settings">
			<div class="bc-shortcode-ui__product-order">
				<span class="bc-shortcode-ui__field-label"><?php esc_html_e( 'Product Order:', 'bigcommerce' ); ?></span>
				<label for="bc-shortcode-ui__product-order--asc">
					<input
							type="radio"
							name="bc-shortcode-ui__product-order"
							id="bc-shortcode-ui__product-order--asc"
							value="asc"
					>
					<span class="bc-shortcode-ui__field-label"><?php esc_html_e( 'Ascending', 'bigcommerce' ); ?></span>
				</label>
				<label for="bc-shortcode-ui__product-order--desc">
					<input
							type="radio"
							name="bc-shortcode-ui__product-order"
							id="bc-shortcode-ui__product-order--desc"
							value="desc"
					>
					<span class="bc-shortcode-ui__field-label"><?php esc_html_e( 'Descending', 'bigcommerce' ); ?></span>
				</label>
			</div>

			<div class="bc-shortcode-ui__product-orderby">
				<span class="bc-shortcode-ui__field-label"><?php esc_html_e( 'Order By:', 'bigcommerce' ); ?></span>
				<label for="bc-shortcode-ui__product-orderby--title">
					<input
							type="radio"
							name="bc-shortcode-ui__product-orderby"
							id="bc-shortcode-ui__product-orderby--title"
							value="title"
					>
					<span class="bc-shortcode-ui__field-label"><?php esc_html_e( 'Title', 'bigcommerce' ); ?></span>
				</label>
				<label for="bc-shortcode-ui__product-orderby--date">
					<input
							type="radio"
							name="bc-shortcode-ui__product-orderby"
							id="bc-shortcode-ui__product-orderby--date"
							value="date"
					>
					<span class="bc-shortcode-ui__field-label"><?php esc_html_e( 'Date', 'bigcommerce' ); ?></span>
				</label>
			</div>

			<div class="bc-shortcode-ui__pagination">
				<label for="bc-shortcode-ui__posts-per-page">
					<span class="bc-shortcode-ui__field-label"><?php esc_html_e( 'Products Per Page:', 'bigcommerce' ); ?></span>
					<div class="bc-shortcode-ui__posts-per-page--control">
						<i class="dashicons dashicons-screenoptions bc-shortcode-ui__posts-per-page-icon"></i>
						<input
								type="range"
								min="1"
								max="100"
								name="bc-shortcode-ui__posts-per-page"
								id="bc-shortcode-ui__posts-per-page"
								class="bc-shortcode-ui__posts-per-page"
								value="<?php esc_attr_e( $posts_per_page ); ?>"
						>
						<span
								class="bc-shortcode-ui__posts-per-page-value"
								data-js="bc-shortcode-ui-posts-per-page-indicator"
								data-default="<?php esc_attr_e( $posts_per_page ); ?>"
						></span>
						<button
								type="button"
								data-js="bc-shortcode-ui-reset-posts-per-page"
								data-reset-value="<?php esc_attr_e( $posts_per_page ); ?>"
								class="bc-shortcode-ui__posts-per-page-reset"
						>
							<?php echo esc_html_x( 'reset', 'reset posts per page to the default value', 'bigcommerce' ); ?>
						</button>
					</div>
				</label>
			</div>

		</div>
		<button class="button button-primary button-large" data-js="bc-shortcode-ui-embed-button">
			<?php esc_html_e( 'Embed Product(s)', 'bigcommerce' ); ?>
		</button>
	</section>
</aside>