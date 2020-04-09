<?php


namespace BigCommerce\Taxonomies\Brand;


use BigCommerce\Customizer\Sections\Product_Archive;
use BigCommerce\Post_Types\Product\Product;
use BigCommerce\Taxonomies\Taxonomy_Config;

class Config extends Taxonomy_Config {

	/**
	 * Arguments to pass when registering the taxonomy.
	 *
	 * Filterable with register_taxonomy_args
	 *
	 * @return array
	 * @see register_taxonomy() for accepted args.
	 */
	public function get_args() {
		return [
			'hierarchical'       => false,
			'public'             => true,
			'show_ui'            => true,
			'show_in_nav_menus'  => true,
			'show_in_quick_edit' => false,
			'show_in_rest'       => true,
			'labels'             => $this->get_labels(),
			'rewrite'            => [
				'slug'       => $this->get_slug(),
				'with_front' => false,
			],
			'capabilities'       => $this->get_caps(),
		];
	}

	private function get_slug() {
		$slug = _x( 'brands', 'taxonomy rewrite slug', 'bigcommerce' );
		$setting = get_option( Product_Archive::BRAND_SLUG, $slug );
		if ( ! empty( $setting ) ) {
			$slug = $setting;
		}
		return trailingslashit( $this->get_products_slug() ) . sanitize_title( trim( $slug, '/') );
	}

	/**
	 * Get labels for the taxonomy.
	 * Filterable with taxonomy_labels_{$taxonomy_name}
	 *
	 * @return array
	 */
	public function get_labels() {
		return [
			'name'                       => _x( 'Brands', 'taxonomy general name', 'bigcommerce' ),
			'menu_name'                  => _x( 'Brands', 'taxonomy menu name', 'bigcommerce' ),
			'singular_name'              => _x( 'Brand', 'taxonomy singular name', 'bigcommerce' ),
			'search_items'               => __( 'Search Brands', 'bigcommerce' ),
			'popular_items'              => __( 'Popular Brands', 'bigcommerce' ),
			'all_items'                  => __( 'All Brands', 'bigcommerce' ),
			'parent_item'                => __( 'Parent Brand', 'bigcommerce' ),
			'parent_item_colon'          => __( 'Parent Brand:', 'bigcommerce' ),
			'edit_item'                  => __( 'Edit Brand', 'bigcommerce' ),
			'view_item'                  => __( 'View Brand', 'bigcommerce' ),
			'update_item'                => __( 'Update Brand', 'bigcommerce' ),
			'add_new_item'               => __( 'Add New Brand', 'bigcommerce' ),
			'new_item_name'              => __( 'New Brand Name', 'bigcommerce' ),
			'separate_items_with_commas' => __( 'Separate brands with commas', 'bigcommerce' ),
			'add_or_remove_items'        => __( 'Add or remove brands', 'bigcommerce' ),
			'choose_from_most_used'      => __( 'Choose from the most used brands', 'bigcommerce' ),
			'not_found'                  => __( 'No brands found.', 'bigcommerce' ),
			'no_terms'                   => __( 'No brands', 'bigcommerce' ),
			'items_list_navigation'      => __( 'Brands list navigation', 'bigcommerce' ),
			'items_list'                 => __( 'Brands list', 'bigcommerce' ),
			'most_used'                  => _x( 'Most Used', 'brands tab header', 'bigcommerce' ),
			'back_to_items'              => __( '&larr; Back to Brands', 'bigcommerce' ),
		];
	}
}
