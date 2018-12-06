<?php


namespace BigCommerce\Post_Types\Product;


use BigCommerce\Customizer\Sections\Product_Archive;
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
			'hierarchical'    => false,
			'menu_icon'       => 'dashicons-bigcommerce',
			'map_meta_cap'    => true,
			'supports'        => [ 'title', 'editor', 'thumbnail', 'revisions', 'comments', ],
			/**
			 * Filter the default capability type for editing Product posts.
			 *
			 * @param string $capability_type The base capability string
			 */
			'capability_type' => apply_filters( 'bigcommerce/post_type/product/capabilities', 'post' ),
			'capabilities'    => [
				'create_posts' => 'do_not_allow',
			],
			'public'          => true,
			'has_archive'     => true,
			'rewrite'         => [
				'slug'       => $this->get_slug(),
				'with_front' => false,
			],
			'labels'          => $this->get_labels(),
			'show_in_rest'    => true,
		];
	}

	private function get_slug() {
		$slug    = _x( 'products', 'post type rewrite slug', 'bigcommerce' );
		$setting = get_option( Product_Archive::ARCHIVE_SLUG, $slug );
		if ( ! empty( $setting ) ) {
			$slug = $setting;
		}

		return sanitize_title( trim( $slug, '/' ) );
	}


	/**
	 * Get labels for the post type.
	 * Filterable with post_type_labels_{$post_type_name}
	 *
	 * @return array
	 */
	public function get_labels() {
		return [
			'name'                     => _x( 'Products', 'post type general name', 'bigcommerce' ),
			'menu_name'                => _x( 'BigCommerce', 'bigcommerce' ),
			'singular_name'            => _x( 'Product', 'post type singular name', 'bigcommerce' ),
			'add_new'                  => _x( 'Add New', 'product', 'bigcommerce' ),
			'add_new_item'             => __( 'Add New Product', 'bigcommerce' ),
			'edit_item'                => __( 'Edit Product', 'bigcommerce ' ),
			'new_item'                 => __( 'New Product', 'bigcommerce' ),
			'view_item'                => __( 'View Product', 'bigcommerce' ),
			'view_items'               => __( 'View Products', 'bigcommerce' ),
			'search_items'             => __( 'Search Products', 'bigcommerce' ),
			'not_found'                => __( 'No products found.', 'bigcommerce' ),
			'not_found_in_trash'       => __( 'No products found in Trash.', 'bigcommerce' ),
			'parent_item_colon'        => __( 'Parent Product:', 'bigcommerce' ),
			'all_items'                => __( 'Products', 'bigcommerce' ),
			'archives'                 => __( 'Product Archives', 'bigcommerce' ),
			'attributes'               => __( 'Product Attributes', 'bigcommerce' ),
			'insert_into_item'         => __( 'Insert into product', 'bigcommerce' ),
			'uploaded_to_this_item'    => __( 'Uploaded to this product', 'bigcommerce' ),
			'featured_image'           => _x( 'Featured Image', 'product', 'bigcommerce' ),
			'set_featured_image'       => _x( 'Set featured image', 'product', 'bigcommerce' ),
			'remove_featured_image'    => _x( 'Remove featured image', 'product', 'bigcommerce' ),
			'use_featured_image'       => _x( 'Use as featured image', 'product', 'bigcommerce' ),
			'filter_items_list'        => __( 'Filter products list', 'bigcommerce' ),
			'items_list_navigation'    => __( 'Products list navigation', 'bigcommerce' ),
			'items_list'               => __( 'Products list', 'bigcommerce' ),
			'item_published'           => __( 'Product published.', 'bigcommerce' ),
			'item_published_privately' => __( 'Product published privately.', 'bigcommerce' ),
			'item_reverted_to_draft'   => __( 'Product reverted to draft.', 'bigcommerce' ),
			'item_scheduled'           => __( 'Product scheduled.', 'bigcommerce' ),
			'item_update'              => __( 'Product updated.', 'bigcommerce' ),
		];
	}
}