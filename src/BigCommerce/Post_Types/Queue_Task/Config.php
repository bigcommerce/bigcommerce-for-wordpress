<?php


namespace BigCommerce\Post_Types\Queue_Task;


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
			'name'                     => _x( 'Queue Tasks', 'post type general name', 'bigcommerce' ),
			'menu_name'                => _x( 'Queue Tasks', 'bigcommerce' ),
			'singular_name'            => _x( 'Queue Task', 'post type singular name', 'bigcommerce' ),
			'add_new'                  => _x( 'Add New', 'product', 'bigcommerce' ),
			'add_new_item'             => __( 'Add New Task', 'bigcommerce' ),
			'edit_item'                => __( 'Edit Task', 'bigcommerce ' ),
			'new_item'                 => __( 'New Task', 'bigcommerce' ),
			'view_item'                => __( 'View Task', 'bigcommerce' ),
			'view_items'               => __( 'View Tasks', 'bigcommerce' ),
			'search_items'             => __( 'Search Tasks', 'bigcommerce' ),
			'not_found'                => __( 'No tasks found.', 'bigcommerce' ),
			'not_found_in_trash'       => __( 'No tasks found in Trash.', 'bigcommerce' ),
			'parent_item_colon'        => __( 'Parent Task:', 'bigcommerce' ),
			'all_items'                => __( 'Tasks', 'bigcommerce' ),
			'archives'                 => __( 'Task Archives', 'bigcommerce' ),
			'attributes'               => __( 'Task Attributes', 'bigcommerce' ),
			'insert_into_item'         => __( 'Insert into task', 'bigcommerce' ),
			'uploaded_to_this_item'    => __( 'Uploaded to this task', 'bigcommerce' ),
			'featured_image'           => _x( 'Featured Image', 'product', 'bigcommerce' ),
			'set_featured_image'       => _x( 'Set featured image', 'product', 'bigcommerce' ),
			'remove_featured_image'    => _x( 'Remove featured image', 'product', 'bigcommerce' ),
			'use_featured_image'       => _x( 'Use as featured image', 'product', 'bigcommerce' ),
			'filter_items_list'        => __( 'Filter tasks list', 'bigcommerce' ),
			'items_list_navigation'    => __( 'Tasks list navigation', 'bigcommerce' ),
			'items_list'               => __( 'Tasks list', 'bigcommerce' ),
			'item_published'           => __( 'Task published.', 'bigcommerce' ),
			'item_published_privately' => __( 'Task published privately.', 'bigcommerce' ),
			'item_reverted_to_draft'   => __( 'Task reverted to draft.', 'bigcommerce' ),
			'item_scheduled'           => __( 'Task scheduled.', 'bigcommerce' ),
			'item_update'              => __( 'Task updated.', 'bigcommerce' ),
		];
	}
}