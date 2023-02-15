<?php

namespace BigCommerce\Manager;

use BigCommerce\Taxonomies\Channel\BC_Status;

class Channel_Update_Task implements Task {

	public function handle( array $args ): bool {
		$status     = $args['status'];

		/**
		 * @var \WP_Term
		 */
		$term = $args['term'];

		update_term_meta( $term->term_id, BC_Status::STATUS, $status );


		return true;
	}

}
