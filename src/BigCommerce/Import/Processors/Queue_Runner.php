<?php


namespace BigCommerce\Import\Processors;


use BigCommerce\Api\v3\CatalogApi;
use BigCommerce\Import\Product_Importer;
use BigCommerce\Import\Product_Remover;
use BigCommerce\Import\Runner\Status;

class Queue_Runner implements Import_Processor {
	/**
	 * @var CatalogApi API instanced used for importing products
	 */
	private $api;

	/**
	 * @var int Number of items to process from the queue per batch
	 */
	private $batch;

	/**
	 * @var int Maximum number of times to attempt to import a product before giving up on it
	 */
	private $max_attempts;

	/**
	 * Queue_Runner constructor.
	 *
	 * @param CatalogApi $api
	 * @param int        $batch
	 * @param int        $max_attempts
	 */
	public function __construct( CatalogApi $api, $batch = 5, $max_attempts = 10 ) {
		$this->api          = $api;
		$this->batch        = (int) $batch;
		$this->max_attempts = (int) $max_attempts;
	}

	public function run() {
		$status = new Status();
		$status->set_status( Status::PROCESSING_QUEUE );

		/** @var \wpdb $wpdb */
		global $wpdb;

		$query   = "SELECT SQL_CALC_FOUND_ROWS * FROM {$wpdb->bc_import_queue} ORDER BY attempts ASC, priority DESC, date_created ASC, date_modified ASC LIMIT {$this->batch}";
		$records = $wpdb->get_results( $query );
		$total   = $wpdb->get_var( 'SELECT FOUND_ROWS()' );

		foreach ( $records as $import ) {
			$wpdb->update(
				$wpdb->bc_import_queue,
				[ 'attempts' => $import->attempts + 1, 'last_attempt' => date( 'Y-m-d H:i:s' ), ],
				[ 'bc_id' => $import->bc_id ],
				[ '%d', '%s' ],
				[ '%d' ]
			);
			switch ( $import->import_action ) {
				case 'update':
				case 'ignore':
					$importer = new Product_Importer( $import->bc_id, $this->api );
					$post_id  = $importer->import();
					if ( ! empty( $post_id ) || $import->attempts > $this->max_attempts ) {
						$wpdb->delete( $wpdb->bc_import_queue, [ 'bc_id' => $import->bc_id ], [ '%d' ] );
					}
					break;
				case 'delete':
					$remover = new Product_Remover( $import->bc_id );
					$remover->remove();
					$wpdb->delete( $wpdb->bc_import_queue, [ 'bc_id' => $import->bc_id ], [ '%d' ] );
					break;
				default:
					// how did we get here?
					$wpdb->delete( $wpdb->bc_import_queue, [ 'bc_id' => $import->bc_id ], [ '%d' ] );
					break;
			}

		}

		if ( $total <= count( $records ) ) {
			$status->set_status( Status::PROCESSED_QUEUE );
		}

	}
}