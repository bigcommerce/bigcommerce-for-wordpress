<?php


namespace BigCommerce\Post_Types\Sync_Log;


use BigCommerce\Post_Types\Post_Type_Config;

class Config extends Post_Type_Config {

	/**
	 * Arguments to pass when registering the post type.
	 *
	 * Filterable with register_post_type_args
	 *
	 * @see register_post_type() for accepted args.
	 * @return array
	 */
	public function get_args() {
		return [
			'hierarchical' => false,
			'menu_icon'    => 'dashicons-bigcommerce',
			'map_meta_cap' => false,
			'supports'     => [ 'title', ],
			'public'       => false,
			'has_archive'  => false,
			'labels'       => $this->get_labels(),
			'show_in_rest' => false,
		];
	}

	/**
	 * Get labels for the post type.
	 * Filterable with post_type_labels_{$post_type_name}
	 *
	 * @return array
	 */
	public function get_labels() {
		return [
			'name'                     => _x( 'Sync Logs', 'post type general name', 'bigcommerce' ),
			'menu_name'                => _x( 'Sync Logs', 'bigcommerce' ),
			'singular_name'            => _x( 'Sync Log', 'post type singular name', 'bigcommerce' ),
			'add_new'                  => _x( 'Add New', 'product', 'bigcommerce' ),
			'add_new_item'             => __( 'Add New Sync Log', 'bigcommerce' ),
			'edit_item'                => __( 'Edit Sync Log', 'bigcommerce ' ),
			'new_item'                 => __( 'New Sync Log', 'bigcommerce' ),
			'view_item'                => __( 'View Sync Log', 'bigcommerce' ),
			'view_items'               => __( 'View Sync Logs', 'bigcommerce' ),
			'search_items'             => __( 'Search Sync Logs', 'bigcommerce' ),
			'not_found'                => __( 'No logs found.', 'bigcommerce' ),
			'not_found_in_trash'       => __( 'No logs found in Trash.', 'bigcommerce' ),
			'parent_item_colon'        => __( 'Parent Sync Log:', 'bigcommerce' ),
			'all_items'                => __( 'Sync Logs', 'bigcommerce' ),
			'archives'                 => __( 'Sync Log Archives', 'bigcommerce' ),
			'attributes'               => __( 'Sync Log Attributes', 'bigcommerce' ),
			'insert_into_item'         => __( 'Insert into log', 'bigcommerce' ),
			'uploaded_to_this_item'    => __( 'Uploaded to this log', 'bigcommerce' ),
			'featured_image'           => _x( 'Featured Image', 'product', 'bigcommerce' ),
			'set_featured_image'       => _x( 'Set featured image', 'product', 'bigcommerce' ),
			'remove_featured_image'    => _x( 'Remove featured image', 'product', 'bigcommerce' ),
			'use_featured_image'       => _x( 'Use as featured image', 'product', 'bigcommerce' ),
			'filter_items_list'        => __( 'Filter logs list', 'bigcommerce' ),
			'items_list_navigation'    => __( 'Sync Logs list navigation', 'bigcommerce' ),
			'items_list'               => __( 'Sync Logs list', 'bigcommerce' ),
			'item_published'           => __( 'Sync Log published.', 'bigcommerce' ),
			'item_published_privately' => __( 'Sync Log published privately.', 'bigcommerce' ),
			'item_reverted_to_draft'   => __( 'Sync Log reverted to draft.', 'bigcommerce' ),
			'item_scheduled'           => __( 'Sync Log scheduled.', 'bigcommerce' ),
			'item_update'              => __( 'Sync Log updated.', 'bigcommerce' ),
		];
	}
}