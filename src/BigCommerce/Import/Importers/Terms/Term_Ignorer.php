<?php


namespace BigCommerce\Import\Importers\Terms;

use BigCommerce\Import\Import_Strategy;

/**
 * Handles the logic for skipping the import of a term in the BigCommerce import process. This class implements 
 * the Import_Strategy interface and provides a method for processing a term that should be ignored.
 */
class Term_Ignorer implements Import_Strategy {

	/**
	 * @var \ArrayAccess The BigCommerce term data.
	 */
	protected $bc_term;

	/**
	 * @var string The taxonomy to which the term belongs.
	 */
	protected $taxonomy;

	/**
	 * @var int The term ID in WordPress.
	 */
	protected $term_id;

	/**
	 * Term_Ignorer constructor.
	 *
	 * Initializes the Term_Ignorer with the provided BigCommerce term, taxonomy, and optional term ID.
	 *
	 * @param \ArrayAccess $bc_term The BigCommerce term data.
	 * @param string       $taxonomy The taxonomy the term belongs to.
	 * @param int          $term_id  The WordPress term ID (default is 0).
	 */
	public function __construct( \ArrayAccess $bc_term, $taxonomy, $term_id = 0 ) {
		$this->bc_term  = $bc_term;
		$this->taxonomy = $taxonomy;
		$this->term_id  = $term_id;
	}

	/**
	 * Skips the import of the current term and updates its visibility.
	 *
	 * This method triggers a `do_action` hook indicating the term has been skipped for import 
	 * and updates the term's visibility status in WordPress.
	 *
	 * @return int The term ID after skipping the import.
	 */
	public function do_import() {
		/**
		 * A term has been skipped for import.
		 *
		 * This action is triggered when a term is skipped for import. It allows other functions to react
		 * to the skipping of the term.
		 *
		 * @param array  $bc_term  The BigCommerce term data.
		 * @param string $taxonomy The taxonomy of the term.
		 * @param int    $term_id  The term ID in WordPress.
		 */
		do_action( 'bigcommerce/import/term/skipped', $this->bc_term, $this->taxonomy, $this->term_id );
		update_term_meta( $this->term_id, 'is_visible', ( int ) $this->bc_term[ 'is_visible' ] );

		return $this->term_id;
	}

}
