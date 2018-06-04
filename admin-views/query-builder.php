<?php
/**
 * Query Builder sidebar for the Admin UI Dialog Template.
 *
 * @package BigCommerce Admin
 *
 * @var \WP_Term|false $featured
 * @var \WP_Term|false $sale
 * @var \WP_Term[]     $brands
 * @var \WP_Term[]     $categories
 */

use BigCommerce\Taxonomies\Brand\Brand;
use BigCommerce\Taxonomies\Flag\Flag;
use BigCommerce\Taxonomies\Product_Category\Product_Category;

?>

<aside class="bc-shortcode-ui__selectors" role="complementary">
	<figure class="bc-shortcode-ui__logo" aria-label="<?php esc_attr_e( 'BigCommerce Product Query Builder', 'bigcommerce' ); ?>"></figure>

	<ul class="bc-shortcode-ui__query-builder-list" data-js="bcqb-list">
		<li class="bc-shortcode-ui__query-builder-list-item">
			<a href="#" class="bc-shortcode-ui__query-builder-anchor"
			   data-key="recent"
			   data-value="1"><?php esc_html_e( 'Recent', 'bigcommerce' ); ?></a>
		</li>
		<?php if ( $featured ) { ?>
			<li class="bc-shortcode-ui__query-builder-list-item">
				<a href="#" class="bc-shortcode-ui__query-builder-anchor"
				   data-key="<?php echo esc_attr( Flag::NAME ); ?>"
				   data-value="<?php echo intval( $featured->term_id ); ?>"><?php esc_html_e( 'Featured', 'bigcommerce' ); ?></a>
			</li>
		<?php } ?>
		<?php if ( $sale ) { ?>
			<li class="bc-shortcode-ui__query-builder-list-item">
				<a href="#" class="bc-shortcode-ui__query-builder-anchor"
				   data-key="<?php echo esc_attr( Flag::NAME ); ?>"
				   data-value="<?php echo intval( $sale->term_id ); ?>"><?php esc_html_e( 'On Sale', 'bigcommerce' ); ?></a>
			</li>
		<?php } ?>
		<?php if ( $brands ) { ?>
			<li class="bc-shortcode-ui__query-builder-list-item">
				<button type="button" class="bc-shortcode-ui__query-builder-toggle" data-js="bcqb-has-child-list">
					<?php esc_html_e( 'Brands', 'bigcommerce' ); ?> <i class="bc-icon
					icon-bc-arrow-toggle"></i></button>
				<ul class="bc-shortcode-ui__query-builder-child-list">
					<?php foreach ( $brands as $term ) { ?>
						<li class="bc-shortcode-ui__query-builder-list-item">
							<a href="#" class="bc-shortcode-ui__query-builder-anchor"
							   data-key="<?php echo esc_attr( Brand::NAME ); ?>"
							   data-value="<?php echo intval( $term->term_id ); ?>"><?php echo esc_html( $term->name ); ?></a>
						</li>
					<?php } ?>
				</ul>
			</li>
		<?php } ?>
		<?php if ( $categories ) { ?>
			<li class="bc-shortcode-ui__query-builder-list-item">
				<button type="button" class="bc-shortcode-ui__query-builder-toggle" data-js="bcqb-has-child-list">
					<?php esc_html_e( 'Categories', 'bigcommerce' ); ?> <i class="bc-icon icon-bc-arrow-toggle"></i>
				</button>
				<ul class="bc-shortcode-ui__query-builder-child-list">
					<?php foreach ( $categories as $term ) { ?>
						<li class="bc-shortcode-ui__query-builder-list-item">
							<a href="#" class="bc-shortcode-ui__query-builder-anchor"
							   data-key="<?php echo esc_attr( Product_Category::NAME ); ?>"
							   data-value="<?php echo intval( $term->term_id ); ?>"><?php echo esc_html( $term->name ); ?></a>
						</li>
					<?php } ?>
				</ul>
			</li>
		<?php } ?>
	</ul>
</aside>
