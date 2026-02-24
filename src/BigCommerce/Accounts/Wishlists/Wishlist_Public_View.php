<?php


namespace BigCommerce\Accounts\Wishlists;

use BigCommerce\Templates\Product_Archive;

/**
 * Abstract class for handling wishlist public view actions, including filtering main queries,
 * setting the page title, and modifying results when no items are found.
 */
abstract class Wishlist_Public_View {

	/**
	 * Filter the main query to display wishlist-specific products.
	 *
	 * @param \WP_Query $query The WordPress query object.
	 *
	 * @return void
	 */
	abstract public function filter_main_query( \WP_Query $query );

	/**
	 * Set the page title for the wishlist view.
	 *
	 * @param array $template_data The template data for the current page.
	 *
	 * @return array The modified template data.
	 */
	abstract public function set_page_title( $template_data );

	/**
	 * Set the WordPress title for the wishlist page.
	 *
	 * @param string $title The current title of the page.
	 * @param string $post_type The type of post being queried.
	 *
	 * @return string The modified page title.
	 */
	abstract public function set_wp_title( $title, $post_type );

	/**
	 * Set the no results message for the wishlist page.
	 *
	 * @param array $template_data The template data for the current page.
	 *
	 * @return array The modified template data with updated no results message.
	 */
	abstract public function set_no_results_message( $template_data );

	/**
	 * Remove the refinery component from the product archive template.
	 *
	 * @param array $template_data The template data for the current page.
	 *
	 * @return array The modified template data with the refinery component removed.
	 * @filter bigcommerce/template=components/catalog/product-archive.php/data
	 */
	public function remove_refinery( $template_data ) {
		$template_data[ Product_Archive::REFINERY ] = '';
		return $template_data;
	}
}