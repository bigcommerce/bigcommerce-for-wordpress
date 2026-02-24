<?php


namespace BigCommerce\Import\Importers\Terms;

use BigCommerce\Logging\Error_Log;

/**
 * This class is responsible for updating an existing term in WordPress with data from BigCommerce.
 * It extends the Term_Saver class and implements logic to save term details and associated metadata.
 */
class Term_Updater extends Term_Saver {

	/**
	 * Updates an existing WordPress term with the data from BigCommerce.
	 *
	 * This method constructs the arguments for updating the term and attempts to update it
	 * using WordPress's wp_update_term function. If the update fails, it logs the error.
	 *
	 * @param \ArrayAccess $bc_term The BigCommerce term data to update the WordPress term with.
	 *
	 * @return int The WordPress term ID on success, 0 on failure.
	 */
	protected function save_wp_term( \ArrayAccess $bc_term ) {
		$args = $this->get_term_args( $bc_term );
		$args[ 'name' ] = $this->sanitize_string( $bc_term[ 'name' ] );
		$term = wp_update_term( $this->term_id, $this->taxonomy, $args );
		if ( is_wp_error( $term ) ) {
			do_action( 'bigcommerce/import/log', Error_Log::NOTICE, __( 'Could not update term', 'bigcommerce' ), [
				'term'  => $bc_term,
				'error' => $term->get_error_messages(),
			] );

			return 0;
		}

		return $term[ 'term_id' ];
	}

	/**
	 * Updates the term metadata in WordPress with data from BigCommerce.
	 *
	 * This method saves the BigCommerce-specific metadata for the term such as BigCommerce ID, sort order,
	 * and visibility status.
	 *
	 * @param \ArrayAccess $bc_term The BigCommerce term data to update the metadata with.
	 */
	protected function save_wp_termmeta( \ArrayAccess $bc_term ) {
		update_term_meta( $this->term_id, 'bigcommerce_id', $this->get_term_bc_id( $bc_term ) );
		update_term_meta( $this->term_id, 'sort_order', $bc_term[ 'sort_order' ] );
		update_term_meta( $this->term_id, 'is_visible', ( int ) $bc_term[ 'is_visible' ] );
	}

}
