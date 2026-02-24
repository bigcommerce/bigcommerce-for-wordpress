<?php


namespace BigCommerce\Import\Mappers;


use BigCommerce\Api\v3\ApiException;
use BigCommerce\Api\v3\Api\CatalogApi;
use BigCommerce\Logging\Error_Log;

/**
 * This abstract class provides the base functionality for mapping BigCommerce terms to WordPress terms.
 * It enforces that extending classes set a taxonomy name for mapping. It also provides methods for
 * mapping BigCommerce term IDs to WordPress term IDs and for checking if a term already exists in WordPress.
 */
abstract class Term_Mapper {

	/**
	 * @var string The name of the taxonomy to map. Should be set in the extending class.
	 */
	protected $taxonomy;

	/**
	 * Term_Mapper constructor.
	 *
	 * Ensures that the taxonomy property is set in the extending class.
	 * Throws an exception if the taxonomy is not set.
	 *
	 * @throws \RuntimeException If the taxonomy is not set.
	 */
	public function __construct() {
		if ( empty( $this->taxonomy ) ) {
			throw new \RuntimeException( __( 'Unable to map terms without a taxonomy', 'bigcommerce' ) );
		}
	}

	/**
	 * Map a BigCommerce term ID to the equivalent WordPress term ID.
	 *
	 * This method checks if a BigCommerce term exists in WordPress and returns the corresponding term ID.
	 * If the term is not found, it returns 0, indicating it hasn't been imported yet.
	 *
	 * @param int $bc_id The BigCommerce term ID to map.
	 *
	 * @return int The WordPress term ID or 0 if the term is not found.
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
	 * Find an already-imported term in the WordPress database.
	 *
	 * This method checks the WordPress terms table to find a term with a matching BigCommerce ID.
	 * It returns the term ID if found, or 0 if the term is not found.
	 *
	 * @param int $bc_id The BigCommerce term ID to search for.
	 *
	 * @return int The ID of the found term, or 0 if not found.
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