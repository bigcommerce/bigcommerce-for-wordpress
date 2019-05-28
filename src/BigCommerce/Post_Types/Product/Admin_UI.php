<?php


namespace BigCommerce\Post_Types\Product;


use BigCommerce\Import\Runner\Status;

class Admin_UI {

	/**
	 * Prevent updates to the post slug
	 *
	 * @param array $post_data
	 * @param array $submitted_data
	 *
	 * @return array
	 * @filter wp_insert_post_data
	 */
	public function prevent_slug_changes( $post_data, $submitted_data ) {
		if ( empty( $submitted_data[ 'ID' ] ) || empty( $post_data[ 'post_type' ] ) || $post_data[ 'post_type' ] !== Product::NAME ) {
			return $post_data;
		}

		$original_slug            = wp_slash( get_post_field( 'post_name', $submitted_data[ 'ID' ] ) );
		$post_data[ 'post_name' ] = $original_slug;

		return $post_data;
	}

	/**
	 * Filters the sample permalink HTML markup to make it a static link
	 *
	 * @param string   $html    Sample permalink HTML markup.
	 * @param int      $post_id Post ID.
	 * @param string   $title   New sample permalink title.
	 * @param string   $slug    New sample permalink slug.
	 * @param \WP_Post $post    Post object.
	 *
	 * @return string
	 * @action get_sample_permalink_html
	 */
	public function override_sample_permalink_html( $html, $post_id, $title, $slug, $post ) {
		if ( get_post_type( $post ) !== Product::NAME ) {
			return $html;
		}

		if ( strpos( $html, 'editable-post-name-full' ) === false ) {
			return $html; // it's not an editable permalink
		}

		$html = preg_replace( '#<span id="editable-post-name">(.*?)</span>#', '$1', $html ); // strip out the editable tags
		$html = preg_replace( '#<span id="edit-slug-buttons">.*?</span>#', '', $html ); // remove the edit button
		$html = preg_replace( '#<span id="editable-post-name-full">.*?</span>#', '', $html ); // remove the hidden field

		return $html;
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
	 * @filter views_edit-bigcommerce_product 10
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
	 * Add a link to manage products in BigCommerce to the top
	 * of the products list table
	 *
	 * @param array $views
	 *
	 * @return array
	 * @filter views_edit-bigcommerce_product 2
	 */
	public function list_table_manage_link( $views = [] ) {
		$views[ 'bc-manage-products' ] = sprintf(
			'<a href="%s" target="_blank" rel="noopener">%s</a>',
			esc_url( $this->get_manage_products_url() ),
			__( 'Manage on BigCommerce', 'bigcommerce' )
		);

		return $views;
	}

	private function get_manage_products_url() {
		return 'https://login.bigcommerce.com/deep-links/manage/products';
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

	/**
	 * Show applicable admin notices at the top of the products list table
	 *
	 * @return void
	 * @action admin_notices
	 */
	public function list_table_admin_notices() {
		$screen = get_current_screen();
		if ( $screen && $screen->base === 'edit' && $screen->post_type === Product::NAME ) {
			settings_errors();
		}
	}
}