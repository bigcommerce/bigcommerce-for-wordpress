<?php


namespace BigCommerce\Taxonomies\Product_Type;


use BigCommerce\Taxonomies\Taxonomy_Config;

class Config extends Taxonomy_Config {

	/**
	 * Arguments to pass when registering the taxonomy.
	 *
	 * Filterable with register_taxonomy_args
	 *
	 * @see register_taxonomy() for accepted args.
	 * @return array
	 */
	public function get_args() {
		return [
			'hierarchical' => false,
			'public'       => false,
			'show_ui'      => false,
			'labels'       => $this->get_labels(),
		];
	}

	/**
	 * Get labels for the taxonomy.
	 * Filterable with taxonomy_labels_{$taxonomy_name}
	 *
	 * @return array
	 */
	public function get_labels() {
		return [
			'name'                       => _x( 'Types', 'taxonomy general name', 'bigcommerce' ),
			'menu_name'                  => _x( 'Types', 'taxonomy menu name', 'bigcommerce' ),
			'singular_name'              => _x( 'Type', 'taxonomy singular name', 'bigcommerce' ),
			'search_items'               => __( 'Search Types', 'bigcommerce' ),
			'popular_items'              => __( 'Popular Types', 'bigcommerce' ),
			'all_items'                  => __( 'All Types', 'bigcommerce' ),
			'parent_item'                => __( 'Parent Type', 'bigcommerce' ),
			'parent_item_colon'          => __( 'Parent Type:', 'bigcommerce' ),
			'edit_item'                  => __( 'Edit Type', 'bigcommerce' ),
			'view_item'                  => __( 'View Type', 'bigcommerce' ),
			'update_item'                => __( 'Update Type', 'bigcommerce' ),
			'add_new_item'               => __( 'Add New Type', 'bigcommerce' ),
			'new_item_name'              => __( 'New Type Name', 'bigcommerce' ),
			'separate_items_with_commas' => __( 'Separate types with commas', 'bigcommerce' ),
			'add_or_remove_items'        => __( 'Add or remove types', 'bigcommerce' ),
			'choose_from_most_used'      => __( 'Choose from the most used types', 'bigcommerce' ),
			'not_found'                  => __( 'No types found.', 'bigcommerce' ),
			'no_terms'                   => __( 'No types', 'bigcommerce' ),
			'items_list_navigation'      => __( 'Types list navigation', 'bigcommerce' ),
			'items_list'                 => __( 'Types list', 'bigcommerce' ),
			'most_used'                  => _x( 'Most Used', 'types tab header', 'bigcommerce' ),
			'back_to_items'              => __( '&larr; Back to Types', 'bigcommerce' ),
		];
	}
}