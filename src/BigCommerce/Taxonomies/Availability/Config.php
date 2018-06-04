<?php


namespace BigCommerce\Taxonomies\Availability;


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
			'name'                       => _x( 'Availabilities', 'taxonomy general name', 'bigcommerce' ),
			'menu_name'                  => _x( 'Availabilities', 'taxonomy menu name', 'bigcommerce' ),
			'singular_name'              => _x( 'Availability', 'taxonomy singular name', 'bigcommerce' ),
			'search_items'               => __( 'Search Availabilities', 'bigcommerce' ),
			'popular_items'              => __( 'Popular Availabilities', 'bigcommerce' ),
			'all_items'                  => __( 'All Availabilities', 'bigcommerce' ),
			'parent_item'                => __( 'Parent Availability', 'bigcommerce' ),
			'parent_item_colon'          => __( 'Parent Availability:', 'bigcommerce' ),
			'edit_item'                  => __( 'Edit Availability', 'bigcommerce' ),
			'view_item'                  => __( 'View Availability', 'bigcommerce' ),
			'update_item'                => __( 'Update Availability', 'bigcommerce' ),
			'add_new_item'               => __( 'Add New Availability', 'bigcommerce' ),
			'new_item_name'              => __( 'New Availability Name', 'bigcommerce' ),
			'separate_items_with_commas' => __( 'Separate availabilities with commas', 'bigcommerce' ),
			'add_or_remove_items'        => __( 'Add or remove availabilities', 'bigcommerce' ),
			'choose_from_most_used'      => __( 'Choose from the most used availabilities', 'bigcommerce' ),
			'not_found'                  => __( 'No availabilities found.', 'bigcommerce' ),
			'no_terms'                   => __( 'No availabilities', 'bigcommerce' ),
			'items_list_navigation'      => __( 'Availabilities list navigation', 'bigcommerce' ),
			'items_list'                 => __( 'Availabilities list', 'bigcommerce' ),
			'most_used'                  => _x( 'Most Used', 'availabilities tab header', 'bigcommerce' ),
			'back_to_items'              => __( '&larr; Back to Availabilities', 'bigcommerce' ),
		];
	}
}