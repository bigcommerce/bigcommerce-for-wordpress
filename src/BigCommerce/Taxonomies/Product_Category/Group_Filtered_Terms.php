<?php

namespace BigCommerce\Taxonomies\Product_Category;


use BigCommerce\Accounts\Customer;

class Group_Filtered_Terms {
	/**
	 * @param $args
	 * @param $taxonomies
	 *
	 * @return mixed
	 *
	 * @action get_terms_args
	 */
	public function exclude_hidden_terms( $args, $taxonomies ) {
		if ( ! in_array( Product_Category::NAME, $taxonomies ) ) {
			return $args;
		}

		if ( $args['slug'] || count( $args['include'] ) ) {
			return $args;
		}

		// To prevent calling get_local_term_ids we check and see if we already have that meta_query in place.
		if ( ! empty ( $args['meta_query'] ) && in_array( 'bigcommerce_id', wp_list_pluck( $args['meta_query'], 'key' ) ) ) {
			return $args;
		}

		$visible_terms = $this->get_visible_terms();
		if ( empty( $visible_terms ) ) {
			return $args;
		}

		$args['include'] = $visible_terms;

		return $args;
	}

	/**
	 * @return array
	 *
	 * @filter bigcommerce/product_category/visibility
	 */
	public function get_visible_terms() {
		$visible_terms = $this->get_transient();

		if ( false !== $visible_terms ) {
			return $visible_terms;
		}

		$customer      = new Customer( get_current_user_id() );
		$group_info    = $customer->get_group()->get_info();
		$visible_terms = [];

		if ( ! $this->user_has_term_restrictions($group_info) ) {
			$this->set_transient( $visible_terms );

			return $visible_terms;
		}

		$visible_terms = $this->get_local_term_ids( $group_info['category_access']['categories'] );
		$this->set_transient( $visible_terms );

		return $visible_terms;
	}

	private function user_has_term_restrictions($group_info) {
		if ( ! array_key_exists( 'category_access', $group_info ) ||
		     ! array_key_exists( 'type', $group_info['category_access'] ) ||
		     ! array_key_exists( 'categories', $group_info['category_access'] )
		) {
			return false;
		}

		if ( 'specific' !== $group_info['category_access']['type'] ) {
			return false;
		}

		if ( ! $group_info['category_access']['categories'] ) {
			return false;
		}

		return true;
	}

	private function get_local_term_ids( $category_ids ) {
		return get_terms( [
			'taxonomy'         => Product_Category::NAME,
			'hide_empty'       => false,
			'meta_query'       => [
				[
					'key'     => 'bigcommerce_id',
					'value'   => $category_ids,
					'compare' => 'IN',
				],
			],
			'suppress_filter'  => true,
			'fields'           => 'ids',
		] );
	}

	private function transient_key() {
		$customer_id = get_current_user_id();

		return sprintf( 'bccustomervisibleterms%d', $customer_id );
	}

	private function get_transient() {
		return get_transient( $this->transient_key() );
	}

	private function set_transient( $visible_terms ) {
		/**
		 * Set the cache time for the list of term ids that a group-member user has access to.
		 *
		 * @param int $time The length of time in seconds to cache the terms.
		 */
		$transient_time = apply_filters( 'bigcommerce/product_category/group_filter_terms_user_cache_time', HOUR_IN_SECONDS );

		set_transient(
			$this->transient_key(),
			$visible_terms,
			$transient_time
		);
	}
}