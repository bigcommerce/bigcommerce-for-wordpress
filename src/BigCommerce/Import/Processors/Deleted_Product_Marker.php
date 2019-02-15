<?php


namespace BigCommerce\Import\Processors;


use BigCommerce\Import\Runner\Status;
use BigCommerce\Logging\Error_Log;

class Deleted_Product_Marker implements Import_Processor {
	public function run() {
		$status = new Status();
		$status->set_status( Status::MARKING_DELETED_PRODUCTS );

		/** @var \wpdb $wpdb */
		global $wpdb;

		$query = "SELECT bc_id FROM {$wpdb->bc_products} WHERE bc_id NOT IN ( SELECT bc_id FROM {$wpdb->bc_import_queue} )";
		$ids   = $wpdb->get_col( $query );

		$now = date( 'Y-m-d H:i:s' );
		$inserts = array_map( function ( $bc_id ) use ( $now ) {
			return sprintf( '( %d, "%s", "delete", "%s" )', $bc_id, $now, $now );
		}, $ids );

		$count = 0;
		if ( ! empty( $inserts ) ) {
			do_action( 'bigcommerce/log', Error_Log::DEBUG, sprintf( __( 'Queuing %d products for deletion', 'bigcommerce' ), count( $ids ) ), [
				'product_ids' => $ids,
			] );
			$values = implode( ', ', $inserts );
			$count  = $wpdb->query( "INSERT IGNORE INTO {$wpdb->bc_import_queue} ( bc_id, date_modified, import_action, date_created ) VALUES $values" );
		}

		do_action( 'bigcommerce/import/marked_deleted', $count, $ids );

		$status->set_status( Status::MARKED_DELETED_PRODUCTS );
	}
}