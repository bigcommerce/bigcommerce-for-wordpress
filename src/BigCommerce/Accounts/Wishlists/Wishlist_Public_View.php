<?php


namespace BigCommerce\Accounts\Wishlists;

use BigCommerce\Templates\Product_Archive;

abstract class Wishlist_Public_View {

	abstract public function filter_main_query( \WP_Query $query );

	abstract public function set_page_title( $template_data );

	abstract public function set_wp_title( $title, $post_type );

	abstract public function set_no_results_message( $template_data );

	/**
	 * Remove the refinery component from the product archive template
	 *
	 * @param array $template_data
	 *
	 * @return array
	 * @filter bigcommerce/template=components/catalog/product-archive.php/data
	 */
	public function remove_refinery( $template_data ) {
		$template_data[ Product_Archive::REFINERY ] = '';
		return $template_data;
	}
}