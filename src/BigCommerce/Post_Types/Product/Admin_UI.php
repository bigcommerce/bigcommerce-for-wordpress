<?php


namespace BigCommerce\Post_Types\Product;


use BigCommerce\Import\Runner\Status;

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

		$original_title            = wp_slash( get_post_field( 'post_title', $submitted_data[ 'ID' ] ) );
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

	/**
	 * Add the last import date at the end of the views above the products
	 * list table. While not exactly a view, it's a reasonable place
	 * to inject the status into the UI.
	 *
	 * @param array $views
	 *
	 * @return array
	 * @filter views_edit-bigcommerce_product
	 */
	public function list_table_import_status( $views = [] ) {
		$last_import = $this->last_import_date();

		if ( $last_import ) {
			$views[ 'bc-import-status' ] = sprintf(
				__( 'Last Import %s', 'bigcommerce' ),
				$last_import
			);
		}

		return $views;
	}

	/**
	 * @return string The date of the last import. Empty if not available.
	 */
	public function last_import_date() {
		$status    = new Status();
		$previous  = $status->previous_status();
		$timestamp = strtotime( get_date_from_gmt( date( 'Y-m-d H:i:s', (int) $previous[ 'timestamp' ] ) ) );
		$date      = date_i18n( get_option( 'date_format', 'Y-m-d' ), $timestamp, false );
		switch ( $previous[ 'status' ] ) {
			case Status::COMPLETED:
			case Status::FAILED:
				return $date;
			case Status::NOT_STARTED:
			default:
				return '';
		}
	}
}