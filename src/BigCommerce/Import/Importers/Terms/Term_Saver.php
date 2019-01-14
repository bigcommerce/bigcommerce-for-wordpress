<?php


namespace BigCommerce\Import\Importers\Terms;

use BigCommerce\Import\Import_Strategy;

abstract class Term_Saver implements Import_Strategy {
	/** @var \ArrayAccess */
	protected $bc_term;

	/** @var string */
	protected $taxonomy;

	/** @var int */
	protected $term_id;

	public function __construct( \ArrayAccess $bc_term, $taxonomy, $term_id = 0 ) {
		$this->bc_term  = $bc_term;
		$this->taxonomy = $taxonomy;
		$this->term_id  = $term_id;
	}

	/**
	 * Import the Term into WordPress
	 *
	 * @return int The imported term ID
	 */
	public function do_import() {
		$this->term_id = $this->save_wp_term( $this->bc_term );
		$this->save_wp_termmeta( $this->bc_term );

		return $this->term_id;
	}

	abstract protected function save_wp_term( \ArrayAccess $bc_term );

	abstract protected function save_wp_termmeta( \ArrayAccess $bc_term );

	protected function sanitize_int( $value ) {
		if ( is_scalar( $value ) ) {
			return (int) $value;
		}

		return 0;
	}

	protected function sanitize_string( $value ) {
		if ( is_scalar( $value ) ) {
			return (string) $value;
		}

		return '';
	}

	/**
	 * @param \ArrayAccess $bc_term
	 *
	 * @return array
	 */
	protected function get_term_args( \ArrayAccess $bc_term ) {
		// Wp uses wp_filter_kses to sanitize html from the term description
		// we need to make sure we're getting the same result here
		return [
			'description' => wp_filter_kses( $this->sanitize_string( $bc_term[ 'description' ] ) ),
			'parent' => $this->determine_parent_term_id( $bc_term ),
		];
	}

	/**
	 * Find a previously imported term that should be set as the parent term
	 *
	 * @param \ArrayAccess $bc_term
	 *
	 * @return int
	 */
	protected function determine_parent_term_id( \ArrayAccess $bc_term ) {
		$bc_id = isset( $bc_term[ 'parent_id' ] ) ? $this->sanitize_int( $bc_term[ 'parent_id' ] ) : 0;
		if ( empty( $bc_id ) ) {
			return 0;
		}

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