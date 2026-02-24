<?php


namespace BigCommerce\Import\Importers\Terms;

use BigCommerce\Logging\Error_Log;

/**
 * Class Term_Creator
 *
 * Handles the creation and saving of terms in WordPress during the import process. This class extends Term_Saver 
 * and includes methods for inserting terms and saving term metadata.
 */
class Term_Creator extends Term_Saver {

	/**
	 * Saves a BigCommerce term to WordPress as a WP term.
	 *
	 * This method inserts a new term into WordPress using the `wp_insert_term` function. If an error occurs 
	 * during insertion, a log entry is created using the `bigcommerce/import/log` action.
	 *
	 * @param \ArrayAccess $bc_term The BigCommerce term data to be saved.
	 *
	 * @return int The term ID if the term was successfully created, 0 otherwise.
	 */
	protected function save_wp_term( \ArrayAccess $bc_term ) {
		$term = wp_insert_term( $this->term_name( $bc_term ), $this->taxonomy, $this->get_term_args( $bc_term ) );
		if ( is_wp_error( $term ) ) {
			/**
			 * Action to log messages during the BigCommerce import process.
			 *
			 * This action is triggered during the BigCommerce import process to log important messages. It captures the log level, message, context, and path for further troubleshooting.
			 *
			 * @param string $level The log level (e.g., INFO, ERROR).
			 * @param string $message The log message to be recorded.
			 * @param array $context Additional context for the log entry.
			 * @return void
			 */
			do_action( 'bigcommerce/import/log', Error_Log::NOTICE, __( 'Could not create term', 'bigcommerce' ), [
				'term'  => $bc_term,
				'error' => $term->get_error_messages(),
			] );

			return 0;
		}

		return $term[ 'term_id' ];
	}

	/**
	 * Saves term metadata for a WordPress term.
	 *
	 * This method updates the metadata for a term in WordPress. It stores the BigCommerce ID, sort order, 
	 * and visibility status for the term.
	 *
	 * @param \ArrayAccess $bc_term The BigCommerce term data to update.
	 *
	 * @return void
	 */
	protected function save_wp_termmeta( \ArrayAccess $bc_term ) {
		update_term_meta( $this->term_id, 'bigcommerce_id', $this->get_term_bc_id( $bc_term ) );
		update_term_meta( $this->term_id, 'sort_order', $bc_term[ 'sort_order' ] );
		update_term_meta( $this->term_id, 'is_visible', ( int ) $bc_term[ 'is_visible' ] );
	}

}
