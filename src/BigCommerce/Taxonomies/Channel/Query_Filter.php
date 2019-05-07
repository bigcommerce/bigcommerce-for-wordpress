<?php


namespace BigCommerce\Taxonomies\Channel;

use BigCommerce\Exceptions\Channel_Not_Found_Exception;
use BigCommerce\Post_Types\Product\Product;
use BigCommerce\Taxonomies\Brand\Brand;
use BigCommerce\Taxonomies\Product_Category\Product_Category;

/**
 * Class Query_Filter
 *
 * Responsible for filtering front-end queries to only show
 * products in the current channel
 */
class Query_Filter {

	/**
	 * Filter the query to show products for the current channel
	 *
	 * @param \WP_Query $query
	 *
	 * @return void
	 * @action pre_get_posts
	 */
	public function apply( \WP_Query $query ) {

		try {
			$connections = new Connections();
			$current_channel = $connections->current();
		} catch ( Channel_Not_Found_Exception $e ) {
			return;
		}

		if ( $this->is_product_archive_query( $query ) && ! $this->has_channel_filter( $query ) ) {
			$this->set_tax_query( $query, $current_channel );
		}

		if ( $this->is_singular_product_query( $query ) ) {
			$this->set_product_query( $query, $current_channel );
		}
	}

	/**
	 * Determine if the query is for an archive that may include products
	 *
	 * @param \WP_Query $query
	 *
	 * @return bool
	 */
	private function is_product_archive_query( \WP_Query $query ) {
		if ( $query->is_singular() ) {
			return false;
		}

		$post_type = $query->get( 'post_type' );
		if ( ! empty( $post_type ) ) {
			if ( is_array( $post_type ) && in_array( Product::NAME, $post_type, true ) ) {
				return true;
			}

			if ( $post_type === 'any' ) {
				return true;
			}

			return $post_type === Product::NAME;
		}

		if ( $query->is_tax( [ Brand::NAME, Product_Category::NAME ] ) ) {
			return true;
		}

		return false;
	}

	/**
	 * Determine if there's already a channel filter at the top level
	 * of the query. This prevents adding a conflicting filter to a request
	 * that's already looking in a specific channel.
	 *
	 * @see Product::by_product_id()
	 *
	 * @param \WP_Query $query
	 *
	 * @return bool
	 */
	private function has_channel_filter( \WP_Query $query ) {
		if ( empty( $query->tax_query ) || empty( $query->tax_query->queries ) ) {
			return false;
		}
		foreach ( $query->tax_query->queries as $tax_query ) {
			if ( is_array( $tax_query ) && array_key_exists( 'taxonomy', $tax_query ) && $tax_query[ 'taxonomy' ] === Channel::NAME ) {
				return true;
			}
		}
		return false;
	}

	/**
	 * Set the tax query so that it excludes products in other channels
	 *
	 * @param \WP_Query $query
	 * @param \WP_Term  $current_channel
	 *
	 * @return void
	 */
	private function set_tax_query( \WP_Query $query, \WP_Term $current_channel ) {

		$other_channels = get_terms( [
			'taxonomy' => Channel::NAME,
			'fields'   => 'tt_ids',
			'exclude'  => $current_channel->term_id,
		] );

		if ( empty( $other_channels ) ) {
			return;
		}

		$filter_query = [
			'relation' => 'AND',
			[
				'taxonomy' => Channel::NAME,
				'terms'    => $other_channels,
				'field'    => 'term_taxonomy_id',
				'operator' => 'NOT IN',
			],
		];

		if ( ! isset( $query->tax_query ) ) {
			$query->tax_query = new \WP_Tax_Query( $filter_query );
		}

		$existing_queries          = $query->tax_query->queries;
		$query->tax_query->queries = $filter_query;
		if ( ! empty( $existing_queries ) ) {
			$query->tax_query->queries[] = $existing_queries;
		}

		$query->query_vars['tax_query'] = $query->tax_query->queries;
	}

	/**
	 * Determine if the query is for a single product
	 *
	 * @param \WP_Query $query
	 *
	 * @return bool
	 */
	private function is_singular_product_query( \WP_Query $query ) {
		// Too early to use $query->is_singular( Product::NAME ). The queried object is set after the query runs.
		if ( ! $query->is_singular() ) {
			return false;
		}
		$post_type = $query->get( 'post_type' );

		return $post_type === Product::NAME;
	}

	/**
	 * Set the query so that it selects the correct product with the given slug for the channel
	 *
	 * @param \WP_Query $query
	 * @param \WP_Term  $current_channel
	 *
	 * @return void
	 */
	private function set_product_query( \WP_Query $query, \WP_Term $current_channel ) {
		/** @var \wpdb $wpdb */
		global $wpdb;

		$name = $query->get( Product::NAME );
		if ( empty( $name ) ) {
			return;
		}

		$sql = "SELECT p.ID
		        FROM {$wpdb->posts} p
		        INNER JOIN {$wpdb->term_relationships} r ON r.object_id=p.ID
		        WHERE p.post_name=%s AND p.post_type=%s AND r.term_taxonomy_id=%d";

		$post_id = $wpdb->get_var( $wpdb->prepare( $sql, $name, Product::NAME, $current_channel->term_taxonomy_id ) ) ?: - 1;

		$query->set( 'p', $post_id );
	}
}
