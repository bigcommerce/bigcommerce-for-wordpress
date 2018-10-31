<?php


namespace BigCommerce\Import;


use BigCommerce\Api\v3\ApiException;
use BigCommerce\Api\v3\Api\CatalogApi;

abstract class Term_Mapper {
	/**
	 * @var string The name of the taxonomy to map. Should be set in the extending class.
	 */
	protected $taxonomy;

	/**
	 * @var CatalogApi
	 */
	protected $api;

	public function __construct( CatalogApi $api ) {
		$this->api = $api;
		if ( empty( $this->taxonomy ) ) {
			throw new \RuntimeException( __( 'Unable to map terms without a taxonomy', 'bigcommerce' ) );
		}
	}

	/**
	 * Map a BigCommerce term ID to the equivalent WP term ID
	 *
	 * @param int $bc_id
	 *
	 * @return int
	 */
	public function map( $bc_id ) {
		if ( empty( $bc_id ) ) {
			return 0;
		}

		$local = $this->find_existing_term( $bc_id );

		if ( $local ) {
			return $local;
		}

		try {
			$bc_term = $this->fetch_from_api( $bc_id );
		} catch ( ApiException $e ) {
			return 0;
		}

		return $this->create_term( $bc_term );
	}

	/**
	 * Fetch the term data from the BigCommerce API
	 *
	 * @param int $bc_id
	 *
	 * @return \ArrayAccess
	 * @throws ApiException
	 */
	abstract protected function fetch_from_api( $bc_id );

	/**
	 * Find an already-imported term in the WP database
	 *
	 * @param int $bc_id
	 *
	 * @return int The ID of the found term. 0 on failure.
	 */
	protected function find_existing_term( $bc_id ) {
		$terms = get_terms( [
			'taxonomy'   => $this->taxonomy,
			'hide_empty' => false,
			'meta_query' => [
				[
					'key'     => 'bigcommerce_id',
					'value'   => $bc_id,
					'compare' => '=',
				],
			],
		] );

		if ( ! empty( $terms ) ) {
			return (int) reset( $terms )->term_id;
		}

		return 0;
	}

	/**
	 * Create a WordPress term from the BigCommerce API object
	 *
	 * @param \ArrayAccess $bc_term
	 *
	 * @return int The ID of the created term
	 */
	protected function create_term( \ArrayAccess $bc_term ) {

		$term = wp_insert_term( $this->sanitize_string( $bc_term[ 'name' ] ), $this->taxonomy, $this->get_term_args( $bc_term ) );
		if ( is_wp_error( $term ) ) {
			return 0;
		}

		update_term_meta( $term[ 'term_id' ], 'bigcommerce_id', $bc_term[ 'id' ] );

		return $term[ 'term_id' ];
	}

	/**
	 * Get additional args to pass to wp_insert_term
	 *
	 * @param \ArrayAccess $bc_term
	 *
	 * @return array
	 */
	abstract protected function get_term_args( \ArrayAccess $bc_term );


	protected function sanitize_int( $value ) {
		if ( is_scalar( $value ) ) {
			return intval( $value );
		}

		return 0;
	}

	protected function sanitize_string( $value ) {
		if ( is_scalar( $value ) ) {
			return (string) $value;
		}

		return '';
	}

}