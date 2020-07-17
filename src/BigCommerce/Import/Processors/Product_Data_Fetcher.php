<?php


namespace BigCommerce\Import\Processors;


use BigCommerce\Api\v3\ApiException;
use BigCommerce\Api\v3\Api\CatalogApi;
use BigCommerce\Api\v3\Model\ProductCollectionResponse;
use BigCommerce\Api\v3\ObjectSerializer;
use BigCommerce\Import\No_Cache_Options;
use BigCommerce\Import\Runner\Status;
use BigCommerce\Logging\Error_Log;
use BigCommerce\Post_Types\Product\Product;
use BigCommerce\Post_Types\Queue_Task\Queue_Task;

class Product_Data_Fetcher implements Import_Processor {
	use No_Cache_Options;

	const STATE_OPTION = 'bigcommerce_import_product_id_fetcher_state';
	const FILTERED_LISTING_MAP = 'bigcommerce_filtered_listing_map';

	/**
	 * @var int
	 */
	private $limit;
	/**
	 * @var CatalogApi
	 */
	private $catalog;

	/**
	 * Product_Data_Fetcher constructor.
	 *
	 * @param CatalogApi $catalog
	 * @param int        $limit Number of products to fetch per request, max of 10 per API limits
	 */
	public function __construct( CatalogApi $catalog, $limit = 10 ) {
		$this->catalog = $catalog;
		$this->limit   = min( $limit, 10 );
	}

	public function run() {
		$status = new Status();
		$status->set_status( Status::FETCHING_PRODUCTS );

		$next = $this->get_next();

		do_action( 'bigcommerce/log', Error_Log::DEBUG, __( 'Retrieving product data', 'bigcommerce' ), [
			'limit' => $this->limit,
			'after' => $next ?: null,
		] );

		$map = $this->get_filtered_listing_map();

		$chunks      = array_chunk( array_keys( $map ), $this->limit );
		$product_ids = isset( $chunks[ $next ] ) ? $chunks[ $next ] : [];

		if ( empty( $product_ids ) ) {
			do_action( 'bigcommerce/log', Error_Log::DEBUG, __( 'No products left to retrieve', 'bigcommerce' ), [] );
			$status->set_status( Status::FETCHED_PRODUCTS );
			$this->clear_state();

			return;
		}

		do_action( 'bigcommerce/log', Error_Log::DEBUG, __( 'Retrieving products found in listings', 'bigcommerce' ), [
			'limit' => $this->limit,
			'ids'   => $product_ids,
		] );
		try {
			$products_response = $this->catalog->getProducts( [
				'id:in'   => $product_ids,
				'include' => [ 'variants', 'custom_fields', 'images', 'videos', 'bulk_pricing_rules', 'options', 'modifiers' ],
				'limit'   => $this->limit,
			] );
		} catch ( ApiException $e ) {
			do_action( 'bigcommerce/import/error', $e->getMessage(), [
				'response' => $e->getResponseBody(),
				'headers'  => $e->getResponseHeaders(),
			] );
			do_action( 'bigcommerce/log', Error_Log::DEBUG, $e->getTraceAsString(), [] );

			return;
		}

		$inserts = array_map( function ( \BigCommerce\Api\v3\Model\Product $product ) use ( $map ) {
			$product_id = $product->getId();
			if ( ! array_key_exists( $product_id, $map ) ) {
				// How did we get here? The API should have only returned the products already in the map
				return [
					'data'    => [
						'product_id' => $product_id,
					],
					'action'  => 'delete',
					'created' => current_time( 'mysql' ),
				];
			}

			return [
				'product_id' => $product_id,
				'action'     => 'update',
				'created'    => current_time( 'mysql' ),
				'data'       => wp_json_encode( [
					'product'  => ObjectSerializer::sanitizeForSerialization( $product ),
					'listings' => $map[ $product_id ],
				] ),
			];
		}, $products_response->getData() );

		$count = 0;
		if ( ! empty( $inserts ) ) {
			do_action( 'bigcommerce/log', Error_Log::DEBUG, __( 'Adding products to the import queue', 'bigcommerce' ), [
				'count' => count( $inserts ),
			] );

			// optimize the inserts
			wp_suspend_cache_invalidation( true );
			wp_defer_term_counting( true );
			wp_defer_comment_counting( true );

			// prevent WordPress filters from mangling the import data
			wp_remove_targeted_link_rel_filters();
			kses_remove_filters();

			foreach ( $inserts as $record ) {
				$task_id = wp_insert_post( [
					'post_type'    => Queue_Task::NAME,
					'post_status'  => $record['action'],
					'post_content' => wp_slash( $record['data'] ),
					'post_date'    => $record['created'],
					'post_name'    => sprintf( 'update-product-%d', $record['product_id'] ),
					'post_title'   => md5( $record['data'] ),
					'menu_order'   => 0,
				], true );
				if ( is_wp_error( $task_id ) ) {
					do_action( 'bigcommerce/log', Error_Log::WARNING, __( 'Error adding record to import queue', 'bigcommerce' ), [
						'product_id' => $record['product_id'],
						'error'      => $task_id->get_error_message(),
					] );
				} else {
					update_post_meta( $task_id, Product::BIGCOMMERCE_ID, $record['product_id'] );
					$count ++;
				}
			}

			// restore the filters we disabled earlier
			kses_init_filters();
			wp_suspend_cache_invalidation( false );
			wp_defer_term_counting( false );
			wp_defer_comment_counting( false );
		}

		/**
		 * Triggered when a batch of products have been fetched from the BigCommerce
		 * API and stored in the import queue
		 *
		 * @param int                       $count        The number of products added to the queue
		 * @param ProductCollectionResponse $api_response The response received from the BigCommerce API
		 */
		do_action( 'bigcommerce/import/fetched_products', $count, $products_response );

		$next ++;
		if ( ! empty( $chunks[ $next ] ) ) {
			do_action( 'bigcommerce/log', Error_Log::DEBUG, __( 'Ready for next page of products', 'bigcommerce' ), [
				'next' => $next,
			] );
			$this->set_next( $next );
		} else {
			$status->set_status( Status::FETCHED_PRODUCTS );
			$this->clear_state();
		}
	}

	private function get_filtered_listing_map() {
		$filtered = $this->get_option( self::FILTERED_LISTING_MAP, [] );
		if ( ! empty( $filtered ) ) {
			return $filtered;
		}
		$map = (array) $this->get_option( Listing_Fetcher::PRODUCT_LISTING_MAP, [] );
		$map = array_filter( $map, function( $listings ) {
			$listings = array_filter( $listings, function( $json ) {
				$listing = json_decode( $json );
				return $listing && isset( $listing->state ) && in_array( $listing->state, [ 'active', 'pending', 'disabled', 'unknown' ] );
			});
			return ! empty( $listings );
		});
		$this->update_option( self::FILTERED_LISTING_MAP, $map );
		return $map;
	}

	private function get_next() {
		$state = $this->get_state();
		if ( ! array_key_exists( 'next', $state ) ) {
			return 0;
		}

		return (int) $state['next'];
	}

	private function set_next( $next ) {
		$state         = $this->get_state();
		$state['next'] = (int) $next;
		$this->set_state( $state );
	}

	private function get_state() {
		$state = $this->get_option( $this->state_option(), [] );
		if ( ! is_array( $state ) ) {
			return [];
		}

		return $state;
	}

	private function set_state( array $state ) {
		$this->update_option( $this->state_option(), $state, false );
	}

	private function clear_state() {
		$this->delete_option( $this->state_option() );
	}

	private function state_option() {
		return self::STATE_OPTION;
	}
}
