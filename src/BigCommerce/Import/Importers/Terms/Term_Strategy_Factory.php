<?php


namespace BigCommerce\Import\Importers\Terms;

use BigCommerce\Import\Import_Strategy;

class Term_Strategy_Factory {
	/** @var \ArrayAccess */
	private $bc_term;
	/** @var string */
	private $taxonomy;

	public function __construct( \ArrayAccess $bc_term, $taxonomy ) {
		$this->bc_term  = $bc_term;
		$this->taxonomy = $taxonomy;
	}

	/**
	 * Identify the strategy for handling the term import based on
	 * the existing data in WP
	 *
	 * @return Import_Strategy
	 */
	public function get_strategy() {
		$matching_term_id = $this->get_matching_term();
		if ( empty( $matching_term_id ) ) {
			return new Term_Creator( $this->bc_term, $this->taxonomy );
		}

		return new Term_Updater( $this->bc_term, $this->taxonomy, $matching_term_id );

	}

	/**
	 * Find an existing term based on the same BigCommerce product
	 *
	 * @return int The WordPress term ID, or 0 if not found
	 */
	private function get_matching_term() {
		$terms = get_terms( [
			'taxonomy'   => $this->taxonomy,
			'hide_empty' => false,
			'meta_query' => [
				[
					'key'     => 'bigcommerce_id',
					'value'   => $this->bc_term[ 'id' ],
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