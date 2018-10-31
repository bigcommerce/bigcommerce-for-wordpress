<?php


namespace BigCommerce\Import\Processors;


use BigCommerce\Api\v3\Api\CatalogApi;
use BigCommerce\Api\v3\Api\ChannelsApi;
use BigCommerce\Import\Product_Importer;
use BigCommerce\Import\Product_Remover;
use BigCommerce\Import\Runner\Status;
use BigCommerce\Settings\Sections\Channels;

class Queue_Runner implements Import_Processor {
	/**
	 * @var CatalogApi Catalog API instance used for importing products
	 */
	private $catalog;

	/**
	 * @var ChannelsApi Channels API instance used for importing products
	 */
	private $channels;

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
	 * @param CatalogApi  $catalog
	 * @param ChannelsApi $channels
	 * @param int         $batch
	 * @param int         $max_attempts
	 */
	public function __construct( CatalogApi $catalog, ChannelsApi $channels, $batch = 5, $max_attempts = 10 ) {
		$this->catalog      = $catalog;
		$this->channels     = $channels;
		$this->batch        = (int) $batch;
		$this->max_attempts = (int) $max_attempts;
	}

	public function run() {
		$status = new Status();
		$status->set_status( Status::PROCESSING_QUEUE );

		$channel_id = get_option( Channels::CHANNEL_ID, 0 );
		if ( empty( $channel_id ) ) {
			do_action( 'bigcommerce/import/error', __( 'Channel ID is not set. Product import canceled.', 'bigcommerce' ) );

			return;
		}

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
					$importer = new Product_Importer( $import->bc_id, $import->listing_id, $this->catalog, $this->channels, $channel_id );
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