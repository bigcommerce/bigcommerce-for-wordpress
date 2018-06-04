<?php


namespace BigCommerce\Post_Types\Product;


class Admin_UI {
	/**
	 * Print the product title as static HTML to replace
	 * the CSS-hidden title field.
	 *
	 * @param \WP_Post $post
	 *
	 * @return void
	 * @action edit_form_before_permalink
	 */
	public function insert_static_title( \WP_Post $post ) {
		if ( $post->post_type !== Product::NAME ) {
			return;
		}
		$title = $post->post_title;
		printf( '<h2 class="product-title" title="%s">%s</h2>', __( 'Title is set on bigcommerce.com', 'bigcommerce' ), esc_html( $title ) );
	}

	/**
	 * Prevent updates to the post title in the event that user
	 * disables the CSS hiding the post title field
	 *
	 * @param array $post_data
	 * @param array $submitted_data
	 *
	 * @return array
	 * @filter wp_insert_post_data
	 */
	public function prevent_title_changes( $post_data, $submitted_data ) {
		if ( empty( $submitted_data[ 'ID' ] ) || empty( $post_data[ 'post_type' ] ) || $post_data[ 'post_type' ] !== Product::NAME ) {
			return $post_data;
		}

		$original_title = wp_slash( get_post_field( 'post_title', $submitted_data[ 'ID' ] ) );
		$post_data[ 'post_title' ] = $original_title;

		return $post_data;
	}

	/**
	 * Remove the featured image metabox to prevent
	 * editing of the automatically assigned featured image
	 *
	 * @return void
	 * @action add_meta_boxes_ . Product::NAME
	 */
	public function remove_featured_image_meta_box( \WP_Post $post ) {
		remove_meta_box( 'postimagediv', get_current_screen(), 'side' );
	}
}