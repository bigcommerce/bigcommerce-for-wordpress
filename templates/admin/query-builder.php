<?php
/**
 * Query Builder sidebar for the Admin UI Dialog Template.
 *
 * @package BigCommerce Admin
 *
 * @var \WP_Term|false $featured
 * @var \WP_Term|false $sale
 * @var array          $brands
 * @var array          $categories
 * @var array          $channels
 */

use BigCommerce\Taxonomies\Brand\Brand;
use BigCommerce\Taxonomies\Channel\Channel;
use BigCommerce\Taxonomies\Flag\Flag;
use BigCommerce\Taxonomies\Product_Category\Product_Category;

?>

<aside class="bc-shortcode-ui__selectors" role="complementary">
	<figure
			class="bc-shortcode-ui__logo"
			aria-label="<?php esc_attr_e( 'BigCommerce Product Query Builder', 'bigcommerce' ); ?>"
	></figure>

	<?php if ( count( $channels ) > 1 ) {
		printf( '<label for="bcqb-channels" class="bc-shortcode-ui__channel-select">%s <select name="%s" id="bcqb-channels" data-js="bcqb-channels">', esc_html( __( 'Select a Channel', 'bigcommerce' ) ), Channel::NAME );
		foreach ( $channels as $channel ) {
			printf( '<option value="%d" %s data-primary="%s">%s</option>', $channel['id'], selected( $channel['primary'], true, false ), $channel['primary'], esc_html( $channel['name'] ) );
		}
		echo '</select></label>';
	} ?>

	<ul class="bc-shortcode-ui__query-builder-list" data-js="bcqb-list">
		<li class="bc-shortcode-ui__query-builder-list-item">
			<a
				href="#"
				class="bc-shortcode-ui__query-builder-anchor"
				data-key="recent"
				data-value="1"
				data-slug="<?php esc_html_e( 'recent', 'bigcommerce' ); ?>"
				data-depth="0"
			><?php esc_html_e( 'Recent', 'bigcommerce' ); ?></a>
		</li>
		<?php if ( $featured ) { ?>
			<li class="bc-shortcode-ui__query-builder-list-item">
				<a
				    href="#"
				    class="bc-shortcode-ui__query-builder-anchor"
				    data-key="<?php echo esc_attr( Flag::NAME ); ?>"
				    data-value="<?php echo intval( $featured->term_id ); ?>"
				    data-slug="<?php echo esc_attr( $featured->slug ); ?>"
				    data-depth="0"
				><?php esc_html_e( 'Featured', 'bigcommerce' ); ?></a>
			</li>
		<?php } ?>
		<?php if ( $sale ) { ?>
			<li class="bc-shortcode-ui__query-builder-list-item">
				<a
				    href="#"
				    class="bc-shortcode-ui__query-builder-anchor"
				    data-key="<?php echo esc_attr( Flag::NAME ); ?>"
				    data-value="<?php echo intval( $sale->term_id ); ?>"
				    data-slug="<?php echo esc_attr( $sale->slug ); ?>"
				    data-depth="0"
				><?php esc_html_e( 'On Sale', 'bigcommerce' ); ?></a>
			</li>
		<?php } ?>
		<?php if ( $brands ) { ?>
			<li class="bc-shortcode-ui__query-builder-list-item" data-js="bcqb-parent-list-item">
				<button type="button" class="bc-shortcode-ui__query-builder-toggle" data-js="bcqb-has-child-list">
					<?php esc_html_e( 'Brands', 'bigcommerce' ); ?> <i class="bc-icon icon-bc-arrow-toggle"></i>
				</button>
				<ul class="bc-shortcode-ui__query-builder-child-list">
					<?php foreach ( $brands as $term ) { ?>
						<li class="bc-shortcode-ui__query-builder-list-item">
							<a
								href="#"
								class="bc-shortcode-ui__query-builder-anchor"
								data-key="<?php echo esc_attr( Brand::NAME ); ?>"
								data-value="<?php echo intval( $term['id'] ); ?>"
								data-slug="<?php echo esc_attr( $term['slug'] ); ?>"
								data-depth="<?php echo esc_attr( $term['depth'] ); ?>"
							><?php echo esc_html( $term['name'] ); ?></a>
						</li>
					<?php } ?>
				</ul>
			</li>
		<?php } ?>
		<?php if ( $categories ) { ?>
			<li class="bc-shortcode-ui__query-builder-list-item" data-js="bcqb-parent-list-item">
				<button type="button" class="bc-shortcode-ui__query-builder-toggle" data-js="bcqb-has-child-list">
					<?php esc_html_e( 'Categories', 'bigcommerce' ); ?> <i class="bc-icon icon-bc-arrow-toggle"></i>
				</button>
				<ul class="bc-shortcode-ui__query-builder-child-list">
					<?php foreach ( $categories as $term ) { ?>
						<li class="bc-shortcode-ui__query-builder-list-item">
							<a
								href="#"
								class="bc-shortcode-ui__query-builder-anchor"
								data-key="<?php echo esc_attr( Product_Category::NAME ); ?>"
								data-value="<?php echo intval( $term['id'] ); ?>"
								data-slug="<?php echo esc_attr( $term['slug'] ); ?>"
								data-depth="<?php echo esc_attr( $term['depth'] ); ?>"
							><?php echo esc_html( $term['name'] ); ?></a>
						</li>
					<?php } ?>
				</ul>
			</li>
		<?php } ?>
	</ul>
</aside>
