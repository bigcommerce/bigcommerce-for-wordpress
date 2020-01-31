<?php

namespace BigCommerce\Taxonomies\Product_Category;


use BigCommerce\Post_Types\Product\Product;
use BigCommerce\Taxonomies\Brand\Brand;

class Query_Filter {

	/**
	 * @var Group_Filtered_Terms
	 */
	private $group_filtered_terms;

	public function __construct( Group_Filtered_Terms $group_filtered_terms ) {
		$this->group_filtered_terms = $group_filtered_terms;
	}

	/**
	 * Filter the query to show products for the whitelisted product categories
	 *
	 * @param \WP_Query $query
	 *
	 * @return void
	 * @action pre_get_posts
	 */
	public function apply( \WP_Query $query ) {
		if ( empty ( $this->group_filtered_terms->get_visible_terms() ) ) {
			return;
		}

		if ( $this->is_product_archive_query( $query ) ) {
			$this->set_tax_query( $query );
		}

		if ( $this->is_singular_product_query( $query ) ) {
			$this->set_product_query( $query );
		}
	}

	/**
	 * When retrieving a term that has children, WP will include the children in the archive unless we specifically
	 * exclude it. This only applies when a group has visibility of a parent term, but not children.
	 *
	 * @param $query
	 *
	 * @action parse_tax_query
	 */
	public function maybe_hide_children( $query ) {
		if ( empty ( $this->group_filtered_terms->get_visible_terms() ) ) {
			return;
		}

		foreach ( $query->tax_query->queries as $id => $tax_query ) {
			if ( is_array( $tax_query ) && array_key_exists( 'taxonomy', $tax_query ) && Product_Category::NAME === $tax_query['taxonomy'] ) {
				$query->tax_query->queries[ $id ]['include_children'] = false;
			}
		}
	}

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

	private function set_tax_query( \WP_Query $query ) {
		$filter_query =
			[
				[
					'taxonomy'         => Product_Category::NAME,
					'terms'            => $this->group_filtered_terms->get_visible_terms(),
					'field'            => 'term_id',
					'operator'         => 'IN',
					'include_children' => false,
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

		$query->query_vars['tax_query']['relation'] = 'AND';
		$query->query_vars['tax_query']             = $query->tax_query->queries;
	}

	private function is_singular_product_query( \WP_Query $query ) {
		// Too early to use $query->is_singular( Product::NAME ). The queried object is set after the query runs.
		if ( ! $query->is_singular() ) {
			return false;
		}
		$post_type = $query->get( 'post_type' );

		return $post_type === Product::NAME;
	}

	private function set_product_query( \WP_Query $query ) {
		/** @var \wpdb $wpdb */
		global $wpdb;

		$name = $query->get( Product::NAME );
		if ( empty( $name ) ) {
			return;
		}

		$sql = "SELECT p.ID
		        FROM {$wpdb->posts} p
		        INNER JOIN {$wpdb->term_relationships} r ON r.object_id=p.ID
		        WHERE p.post_name=%s AND p.post_type=%s AND r.term_taxonomy_id IN (%s)";

		$post_id = $wpdb->get_var(
			$wpdb->prepare(
				$sql,
				$name,
				Product::NAME,
				implode( ', ', array_map( 'intval', $this->group_filtered_terms->get_visible_terms() ) )
			)
		) ?: - 1;

		$query->set( 'p', $post_id );
	}

}