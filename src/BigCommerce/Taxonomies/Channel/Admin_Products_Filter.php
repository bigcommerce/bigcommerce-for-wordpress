<?php


namespace BigCommerce\Taxonomies\Channel;

use BigCommerce\Exceptions\Channel_Not_Found_Exception;
use BigCommerce\Post_Types\Product\Product;

/**
 * Class Admin_Products_Filter
 *
 * Responsible for filtering the products in the admin list table by channel ID
 */
class Admin_Products_Filter {

	/**
	 * Display the channel select above the Products list table
	 *
	 * @param string $post_type
	 * @param string $which
	 *
	 * @return void
	 * @action restrict_manage_posts
	 */
	public function display_channel_select( $post_type, $which ) {
		if ( $post_type !== Product::NAME || $which !== 'top' ) {
			return;
		}

		$connections = new Connections();
		$channel_options = $connections->active();

		if ( count( $channel_options ) <= 1 ) {
			return; // one or zero channels available, so don't display the filter
		}

		try {
			$current = filter_input( INPUT_GET, Channel::NAME, FILTER_SANITIZE_STRING ) ?: $connections->primary()->slug;
		} catch ( Channel_Not_Found_Exception $e ) {
			$current = '';
		}

		printf( '<label for="filter-by-%s" class="screen-reader-text">%s</label>', esc_attr( Channel::NAME ), esc_html( __( 'Filter by channel', 'bigcommerce' ) ) );
		printf( '<select name="%s" id="filter-by-%s">', esc_attr( Channel::NAME ), esc_attr( Channel::NAME ) );
		if ( $current === '' ) {
			// unlikely condition
			printf( '<option value="" selected="selected">%s</option>', esc_html( __( 'Select a channel', 'bigcommerce' ) ) );
		}
		foreach ( $channel_options as $channel ) {
			printf( '<option value="%s" %s>%s</option>', esc_attr( $channel->slug ), selected( $channel->slug, $current, false ), esc_html( $channel->name ) );
		}
		echo '</select>';
	}

	/**
	 * Enforce a channel filter when displaying the products list table
	 * by setting query vars on the request
	 *
	 * @param \WP $request
	 *
	 * @return void
	 * @action parse_request
	 */
	public function filter_list_table_request( \WP $request ) {
		if ( empty( $request->query_vars['post_type'] ) || $request->query_vars['post_type'] !== Product::NAME ) {
			return; // not a product query, let it through
		}
		if ( ! empty( $request->query_vars[ Channel::NAME ] ) ) {
			return; // already filtered by channel, let it through
		}
		try {
			$connections = new Connections();
			$primary = $connections->primary();
		} catch ( Channel_Not_Found_Exception $e ) {
			return; // we don't know which channel to use, let it through
		}
		$request->set_query_var( Channel::NAME, $primary->slug );
	}
}
