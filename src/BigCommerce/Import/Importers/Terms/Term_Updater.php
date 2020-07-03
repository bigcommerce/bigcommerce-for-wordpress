<?php


namespace BigCommerce\Import\Importers\Terms;

use BigCommerce\Logging\Error_Log;

class Term_Updater extends Term_Saver {
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

	protected function save_wp_termmeta( \ArrayAccess $bc_term ) {
		update_term_meta( $this->term_id, 'bigcommerce_id', $bc_term[ 'id' ] );
		update_term_meta( $this->term_id, 'sort_order', $bc_term[ 'sort_order' ] );
	}

}
