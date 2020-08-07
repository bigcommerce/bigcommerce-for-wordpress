<?php


namespace BigCommerce\Import\Processors;


use BigCommerce\Api\v3\Api\CatalogApi;
use BigCommerce\Api\v3\Model\Listing;
use BigCommerce\Api\v3\Model\Product;
use BigCommerce\Api\v3\ObjectSerializer;
use BigCommerce\Import\Importers\Products\Product_Importer;
use BigCommerce\Import\Importers\Products\Product_Remover;
use BigCommerce\Import\Runner\Status;
use BigCommerce\Logging\Error_Log;
use BigCommerce\Post_Types\Queue_Task\Queue_Task;
use BigCommerce\Taxonomies\Channel\Connections;

class Queue_Runner implements Import_Processor {
	/**
	 * @var CatalogApi Catalog API instance used for importing products
	 */
	private $catalog;

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
	 * @param int         $batch
	 * @param int         $max_attempts
	 */
	public function __construct( CatalogApi $catalog, $batch = 5, $max_attempts = 10 ) {
		$this->catalog      = $catalog;
		$this->batch        = (int) $batch;
		$this->max_attempts = (int) $max_attempts;
	}

	public function run() {
		$status = new Status();
		$status->set_status( Status::PROCESSING_QUEUE );

		/** @var \wpdb $wpdb */
		global $wpdb;

		$queue_records = $wpdb->get_results( $wpdb->prepare(
			"SELECT ID, post_title, post_content, post_status, menu_order FROM {$wpdb->posts} WHERE post_type=%s AND post_status IN ('update', 'delete') ORDER BY menu_order ASC, post_date ASC LIMIT %d",
			Queue_Task::NAME,
			$this->batch
		) );

		$connections = new Connections();
		$channels    = $connections->active();

		foreach ( $queue_records as $record ) {
			if ( function_exists( 'set_time_limit' ) && false === strpos( ini_get( 'disable_functions' ), 'set_time_limit' ) && ! ini_get( 'safe_mode' ) ) {
				@set_time_limit( 60 );
			}

			if ( $record->menu_order > $this->max_attempts ) {
				do_action( 'bigcommerce/log', Error_Log::WARNING, __( 'Too many failed attempts to process record, aborting', 'bigcommerce' ), [
					'record' => $record,
				] );
				$this->mark_task_complete( $record->ID );
			} else {
				$wpdb->update(
					$wpdb->posts,
					[ 'menu_order' => $record->menu_order + 1 ],
					[ 'ID' => $record->ID ],
					[ '%d' ],
					[ '%d' ]
				);

				try {
					$this->handle_record( $record, $channels );
					$this->mark_task_complete( $record->ID );
				} catch ( \Exception $e ) {
					do_action( 'bigcommerce/log', Error_Log::WARNING, __( 'Exception while handling record', 'bigcommerce' ), [
						'record_id' => $record->ID,
						'error'     => $e->getMessage(),
					] );
					do_action( 'bigcommerce/log', Error_Log::DEBUG, $e->getTraceAsString(), [] );
				}
			}
		}


		$remaining = (int) $wpdb->get_var( $wpdb->prepare(
			"SELECT COUNT(*) FROM {$wpdb->posts} WHERE post_type=%s AND post_status IN ('update', 'delete')",
			Queue_Task::NAME
		) );
		do_action( 'bigcommerce/log', Error_Log::DEBUG, __( 'Completed import batch', 'bigcommerce' ), [
			'count'     => count( $queue_records ),
			'remaining' => $remaining,
		] );
		if ( $remaining < 1 ) {
			$status->set_status( Status::PROCESSED_QUEUE );
		}

	}

	/**
	 * @param object     $record
	 * @param \WP_Term[] $channels
	 *
	 * @return void
	 */
	private function handle_record( $record, $channels ) {

		$data = json_decode( $record->post_content );
		if ( empty( $data ) ) {
			do_action( 'bigcommerce/log', Error_Log::WARNING, __( 'Invalid data in queue, unable to parse', 'bigcommerce' ), [
				'json_last_error_msg' => json_last_error_msg(),
				'record'              => $record,
			] );

			return;
		}

		do_action( 'bigcommerce/log', Error_Log::DEBUG, __( 'Handling record from import queue', 'bigcommerce' ), [
			'record_id' => $record->ID,
			'name'      => $record->post_title,
			'attempt'   => $record->menu_order,
			'action'    => $record->post_status,
		] );

		if ( $record->post_status === 'update' ) {
			$bigcommerce_id = get_post_meta( $record->ID, \BigCommerce\Post_Types\Product\Product::BIGCOMMERCE_ID, true );
			$this->handle_update( $bigcommerce_id, $data, $channels );
		} elseif ( $record->post_status === 'delete' ) {
			$this->handle_delete( $data, $channels );
		}

	}

	private function handle_update( $bigcommerce_id, $data, $channels = [] ) {

		/** @var Product $product */
		$product = ObjectSerializer::deserialize( $data->product, Product::class );

		if ( ! $product ) {
			do_action( 'bigcommerce/log', Error_Log::WARNING, __( 'Unable to parse product data, removing from queue', 'bigcommerce' ), [
				'product_id' => $bigcommerce_id,
			] );

			return;
		}

		foreach ( $channels as $channel_term ) {
			$this->handle_update_for_channel( $product, $data->listings, $channel_term );
		}
	}

	/**
	 * @param Product  $product
	 * @param object[] $listings
	 * @param \WP_Term $channel_term
	 *
	 * @return void
	 */
	private function handle_update_for_channel( $product, $listings, $channel_term ) {
		if ( empty( $listings->{$channel_term->term_id} ) ) {
			do_action( 'bigcommerce/log', Error_Log::DEBUG, __( 'No listing found for product, removing', 'bigcommerce' ), [
				'product_id' => $product->getId(),
				'channel'    => $channel_term->term_id,
			] );
			$remover = new Product_Remover();
			$remover->remove_by_product_id( $product->getId(), $channel_term );

			return;
		}

		/** @var Listing $listing */
		$listing = ObjectSerializer::deserialize( json_decode( $listings->{$channel_term->term_id} ), Listing::class );

		if ( ! $listing ) {
			do_action( 'bigcommerce/log', Error_Log::WARNING, __( 'Unable to parse listing data, skipping', 'bigcommerce' ), [
				'product_id' => $product->getId(),
				'channel'    => $channel_term->term_id,
			] );

			return;
		}

		$listing_state = $listing->getState();
		if ( in_array( $listing_state, [ 'deleted', 'pending_delete', 'DELETED_GROUP' ] ) ) {
			do_action( 'bigcommerce/log', Error_Log::DEBUG, __( 'Removing product', 'bigcommerce' ), [
				'product_id' => $product->getId(),
				'channel'    => $channel_term->term_id,
				'state'      => $listing_state,
			] );
			$remover = new Product_Remover();
			$remover->remove_by_product_id( $product->getId(), $channel_term );

			return;
		}

		$product_importer = new Product_Importer( $product, $listing, $this->catalog, $channel_term );
		$post_id          = $product_importer->import();

		if ( ! empty( $post_id ) ) {
			do_action( 'bigcommerce/log', Error_Log::DEBUG, __( 'Product imported successfully', 'bigcommerce' ), [
				'product_id' => $product->getId(),
				'channel'    => $channel_term->term_id,
			] );
		}
	}

	/**
	 * @param object     $data
	 * @param \WP_Term[] $channels
	 *
	 * @return void
	 */
	private function handle_delete( $data, $channels = [] ) {
		do_action( 'bigcommerce/log', Error_Log::DEBUG, __( 'Removing product', 'bigcommerce' ), [
			'data' => $data,
		] );
		$remover = new Product_Remover();
		if ( ! empty( $data->post_id ) ) {
			$remover->remove_by_post_id( $data->post_id );
		} elseif ( ! empty( $data->product_id ) ) {
			foreach ( $channels as $channel_term ) {
				$remover->remove_by_product_id( $data->product_id, $channel_term );
			}
		}
	}

	private function mark_task_complete( $record_id ) {
		wp_update_post( [
			'ID'          => $record_id,
			'post_status' => 'trash',
		] );
	}
}
