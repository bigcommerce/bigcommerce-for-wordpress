<?php


namespace BigCommerce\Taxonomies\Flag;


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
			'name'                       => _x( 'Flags', 'taxonomy general name', 'bigcommerce' ),
			'menu_name'                  => _x( 'Flags', 'taxonomy menu name', 'bigcommerce' ),
			'singular_name'              => _x( 'Flag', 'taxonomy singular name', 'bigcommerce' ),
			'search_items'               => __( 'Search Flags', 'bigcommerce' ),
			'popular_items'              => __( 'Popular Flags', 'bigcommerce' ),
			'all_items'                  => __( 'All Flags', 'bigcommerce' ),
			'parent_item'                => __( 'Parent Flag', 'bigcommerce' ),
			'parent_item_colon'          => __( 'Parent Flag:', 'bigcommerce' ),
			'edit_item'                  => __( 'Edit Flag', 'bigcommerce' ),
			'view_item'                  => __( 'View Flag', 'bigcommerce' ),
			'update_item'                => __( 'Update Flag', 'bigcommerce' ),
			'add_new_item'               => __( 'Add New Flag', 'bigcommerce' ),
			'new_item_name'              => __( 'New Flag Name', 'bigcommerce' ),
			'separate_items_with_commas' => __( 'Separate flags with commas', 'bigcommerce' ),
			'add_or_remove_items'        => __( 'Add or remove flags', 'bigcommerce' ),
			'choose_from_most_used'      => __( 'Choose from the most used flags', 'bigcommerce' ),
			'not_found'                  => __( 'No flags found.', 'bigcommerce' ),
			'no_terms'                   => __( 'No flags', 'bigcommerce' ),
			'items_list_navigation'      => __( 'Flags list navigation', 'bigcommerce' ),
			'items_list'                 => __( 'Flags list', 'bigcommerce' ),
			'most_used'                  => _x( 'Most Used', 'conditions tab header', 'bigcommerce' ),
			'back_to_items'              => __( '&larr; Back to Flags', 'bigcommerce' ),
		];
	}
}