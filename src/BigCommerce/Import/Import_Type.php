<?php


namespace BigCommerce\Import;

use BigCommerce\Api\v3\Api\CatalogApi;
use BigCommerce\Api\v3\ApiException;
use BigCommerce\Import\Runner\Status;

class Import_Type {

	const IMPORT_TYPE         = 'bigcommerce_import_type';
	const IMPORT_TYPE_FULL    = 'import_type_full';
	const IMPORT_TYPE_PARTIAL = 'import_type_partial';

	/**
	 * @var CatalogApi
	 */
	private $catalog;

	/**
	 * Import_Type constructor.
	 *
	 * @param CatalogApi  $catalog The Catalog API connection to use for the import
	 */
	public function __construct( CatalogApi $catalog ) {
		$this->catalog = $catalog;
	}

	/**
	 * @return array
	 * 
	 * @filter bigcommerce_modified_product_ids
	 */
	public function fetch_modified_product_ids() {
		try {
			$previous_log = get_option( Status::PREVIOUS_LOG, [] );
			$end_time     = key( $previous_log ); // last key

			if (empty($end_time)) {
				return [];
			}

			$products_response = $this->catalog->getProducts( [
				'include_fields'    => ['id'],
				'date_modified:min' => floor( floatval( $end_time ) ),
			] );

			return array_map( function ( $product ) {
				return $product['id'];
			}, $products_response->getData() );

		} catch ( ApiException $e ) {
			return [];
		}

	}

	/**
	 * Filter task list for partial imports
	 * 
	 * @param array $task_list
	 * @return array
	 * 
	 * @filter bigcommerce/import/task_list
	 */
	public function filter_task_list( $task_list ) {
		$import_type = get_option( self::IMPORT_TYPE );
		if ( $import_type === self::IMPORT_TYPE_PARTIAL ) {
			$task_list = array_filter( $task_list, function ( $task ) {
				$states = [
					Status::STARTED,
					Status::FETCHED_PRODUCTS,
					Status::MARKED_DELETED_PRODUCTS,
					Status::PROCESSED_QUEUE,
					Status::COMPLETED,
				];

				$task_state = $task->get_completion_state();
				if ( in_array( $task_state, $states ) || strpos( $task_state, Status::FETCHED_LISTINGS ) !== false ) {
					return true;
				}

				return false;
			} );

			// Replace mark products task with an empty one as this task is needed when counting products to process.
			$task_list = array_map( function( $task ) {
				if ( $task->get_completion_state() === Status::MARKED_DELETED_PRODUCTS ) {
					return new Task_Definition( function() {
						$status = new Status();
						$status->set_status( Status::MARKED_DELETED_PRODUCTS );
					}, 60, Runner\Status::MARKED_DELETED_PRODUCTS, [], __( 'Counting products to process', 'bigcommerce' ) );
				}

				return $task;
			}, $task_list );
		}

		return $task_list;
	}

}
