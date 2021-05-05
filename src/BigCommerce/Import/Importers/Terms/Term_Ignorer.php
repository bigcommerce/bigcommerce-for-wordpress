<?php


namespace BigCommerce\Import\Importers\Terms;

use BigCommerce\Import\Import_Strategy;

class Term_Ignorer implements Import_Strategy {

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

	public function do_import() {
		/**
		 * A term has been skipped for import
		 * 
		 * @param array  $bc_term
		 * @param string $taxonomy
		 * @param int    $term_id
		 */
		do_action( 'bigcommerce/import/term/skipped', $this->bc_term, $this->taxonomy, $this->term_id );

		return $this->term_id;
	}

}