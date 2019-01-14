<?php


namespace BigCommerce\Import\Mappers;


use BigCommerce\Api\v3\ApiException;
use BigCommerce\Api\v3\Api\CatalogApi;
use BigCommerce\Logging\Error_Log;

abstract class Term_Mapper {
	/**
	 * @var string The name of the taxonomy to map. Should be set in the extending class.
	 */
	protected $taxonomy;

	public function __construct() {
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

		return 0; // don't import it right now, presume it will be imported on the next importer run
	}

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

}