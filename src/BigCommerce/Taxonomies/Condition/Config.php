<?php


namespace BigCommerce\Taxonomies\Condition;


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
			'name'                       => _x( 'Conditions', 'taxonomy general name', 'bigcommerce' ),
			'menu_name'                  => _x( 'Conditions', 'taxonomy menu name', 'bigcommerce' ),
			'singular_name'              => _x( 'Condition', 'taxonomy singular name', 'bigcommerce' ),
			'search_items'               => __( 'Search Conditions', 'bigcommerce' ),
			'popular_items'              => __( 'Popular Conditions', 'bigcommerce' ),
			'all_items'                  => __( 'All Conditions', 'bigcommerce' ),
			'parent_item'                => __( 'Parent Condition', 'bigcommerce' ),
			'parent_item_colon'          => __( 'Parent Condition:', 'bigcommerce' ),
			'edit_item'                  => __( 'Edit Condition', 'bigcommerce' ),
			'view_item'                  => __( 'View Condition', 'bigcommerce' ),
			'update_item'                => __( 'Update Condition', 'bigcommerce' ),
			'add_new_item'               => __( 'Add New Condition', 'bigcommerce' ),
			'new_item_name'              => __( 'New Condition Name', 'bigcommerce' ),
			'separate_items_with_commas' => __( 'Separate conditions with commas', 'bigcommerce' ),
			'add_or_remove_items'        => __( 'Add or remove conditions', 'bigcommerce' ),
			'choose_from_most_used'      => __( 'Choose from the most used conditions', 'bigcommerce' ),
			'not_found'                  => __( 'No conditions found.', 'bigcommerce' ),
			'no_terms'                   => __( 'No conditions', 'bigcommerce' ),
			'items_list_navigation'      => __( 'Conditions list navigation', 'bigcommerce' ),
			'items_list'                 => __( 'Conditions list', 'bigcommerce' ),
			'most_used'                  => _x( 'Most Used', 'conditions tab header', 'bigcommerce' ),
			'back_to_items'              => __( '&larr; Back to Conditions', 'bigcommerce' ),
		];
	}
}