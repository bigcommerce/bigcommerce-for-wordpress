<?php


namespace BigCommerce\Taxonomies\Channel;


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
			'name'                       => _x( 'Channels', 'taxonomy general name', 'bigcommerce' ),
			'menu_name'                  => _x( 'Channels', 'taxonomy menu name', 'bigcommerce' ),
			'singular_name'              => _x( 'Channel', 'taxonomy singular name', 'bigcommerce' ),
			'search_items'               => __( 'Search Channels', 'bigcommerce' ),
			'popular_items'              => __( 'Popular Channels', 'bigcommerce' ),
			'all_items'                  => __( 'All Channels', 'bigcommerce' ),
			'parent_item'                => __( 'Parent Channel', 'bigcommerce' ),
			'parent_item_colon'          => __( 'Parent Channel:', 'bigcommerce' ),
			'edit_item'                  => __( 'Edit Channel', 'bigcommerce' ),
			'view_item'                  => __( 'View Channel', 'bigcommerce' ),
			'update_item'                => __( 'Update Channel', 'bigcommerce' ),
			'add_new_item'               => __( 'Add New Channel', 'bigcommerce' ),
			'new_item_name'              => __( 'New Channel Name', 'bigcommerce' ),
			'separate_items_with_commas' => __( 'Separate channels with commas', 'bigcommerce' ),
			'add_or_remove_items'        => __( 'Add or remove channels', 'bigcommerce' ),
			'choose_from_most_used'      => __( 'Choose from the most used channels', 'bigcommerce' ),
			'not_found'                  => __( 'No channels found.', 'bigcommerce' ),
			'no_terms'                   => __( 'No channels', 'bigcommerce' ),
			'items_list_navigation'      => __( 'Channels list navigation', 'bigcommerce' ),
			'items_list'                 => __( 'Channels list', 'bigcommerce' ),
			'most_used'                  => _x( 'Most Used', 'conditions tab header', 'bigcommerce' ),
			'back_to_items'              => __( '&larr; Back to Channels', 'bigcommerce' ),
		];
	}
}