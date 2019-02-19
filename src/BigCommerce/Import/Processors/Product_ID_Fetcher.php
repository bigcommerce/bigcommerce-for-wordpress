<?php


namespace BigCommerce\Import\Processors;


use BigCommerce\Api\v3\Api\ChannelsApi;
use BigCommerce\Api\v3\ApiException;
use BigCommerce\Api\v3\Api\CatalogApi;
use BigCommerce\Api\v3\Model\Listing;
use BigCommerce\Api\v3\Model\ListingCollectionResponse;
use BigCommerce\Api\v3\ObjectSerializer;
use BigCommerce\Import\No_Cache_Options;
use BigCommerce\Import\Runner\Status;
use BigCommerce\Logging\Error_Log;
use BigCommerce\Settings\Sections\Channels;

class Product_ID_Fetcher implements Import_Processor {
	use No_Cache_Options;

	const STATE_OPTION = 'bigcommerce_import_product_id_fetcher_state';

	/**
	 * @var ChannelsApi
	 */
	private $channels;

	/**
	 * @var int
	 */
	private $limit;
	/**
	 * @var CatalogApi
	 */
	private $catalog;

	/**
	 * Product_ID_Fetcher constructor.
	 *
	 * @param CatalogApi  $catalog
	 * @param ChannelsApi $channels The Channels API connection to use for the import
	 * @param int         $limit    Number of product IDs to fetch per request
	 */
	public function __construct( CatalogApi $catalog, ChannelsApi $channels, $limit = 100 ) {
		$this->catalog  = $catalog;
		$this->limit    = $limit;
		$this->channels = $channels;
	}

	public function run() {
		$status = new Status();
		$status->set_status( Status::FETCHING_PRODUCT_IDS );

		$channel_id = get_option( Channels::CHANNEL_ID, 0 );
		if ( empty( $channel_id ) ) {
			do_action( 'bigcommerce/import/error', __( 'Channel ID is not set. Product import canceled.', 'bigcommerce' ) );

			return;
		}

		$next = $this->get_next();

		do_action( 'bigcommerce/log', Error_Log::DEBUG, __( 'Retrieving listings', 'bigcommerce' ), [
			'limit' => $this->limit,
			'after' => $next ?: null,
		] );

		try {
			$listings_response = $this->channels->listChannelListings( $channel_id, [
				'limit' => $this->limit,
				'after' => $next ?: null,
			] );
			$listings          = $listings_response->getData();
		} catch ( ApiException $e ) {
			do_action( 'bigcommerce/import/error', $e->getMessage(), [
				'response' => $e->getResponseBody(),
				'headers'  => $e->getResponseHeaders(),
			] );

			return;
		}

		$product_ids = array_map( function ( Listing $listing ) {
			return (int) $listing->getProductId();
		}, $listings );


		do_action( 'bigcommerce/log', Error_Log::DEBUG, __( 'Retrieving products found in listings', 'bigcommerce' ), [
			'limit' => $this->limit,
			'ids'   => $product_ids,
		] );
		try {
			$products_response = $this->catalog->getProducts( [
				'id:in'   => $product_ids,
				'include' => [ 'variants', 'custom_fields', 'images', 'bulk_pricing_rules' ],
				'limit'   => $this->limit,
			] );
			$products          = [];
			foreach ( $products_response->getData() as $product ) {
				$products[ $product->getId() ] = $product;
			}
		} catch ( ApiException $e ) {
			do_action( 'bigcommerce/import/error', $e->getMessage(), [
				'response' => $e->getResponseBody(),
				'headers'  => $e->getResponseHeaders(),
			] );

			return;
		}

		/** @var \wpdb $wpdb */
		global $wpdb;
		$inserts = array_filter( array_map( function ( Listing $listing ) use ( $products ) {
			$modified = $listing->getDateModified() ?: $listing->getDateCreated() ?: new \DateTime();
			$action   = ! in_array( $listing->getState(), [ 'DELETED_GROUP', 'deleted' ] ) ? 'update' : 'delete';

			$product_id = $listing->getProductId();
			if ( ! array_key_exists( $product_id, $products ) ) {
				return false;
			}

			return sprintf(
				'( %d, %d, "%s", "%s", "%s", "%s", "%s" )',
				$product_id,
				$listing->getListingId(),
				$modified->format( 'Y-m-d H:i:s' ),
				$action, date( 'Y-m-d H:i:s' ),
				esc_sql( json_encode( ObjectSerializer::sanitizeForSerialization( $products[ $product_id ] ) ) ),
				esc_sql( json_encode( ObjectSerializer::sanitizeForSerialization( $listing ) ) )
			);
		}, $listings ) );

		$count = 0;
		if ( ! empty( $inserts ) ) {
			do_action( 'bigcommerce/log', Error_Log::DEBUG, __( 'Adding products to the import queue', 'bigcommerce' ), [
				'count' => count( $inserts ),
			] );
			$values = implode( ', ', $inserts );
			$count  = $wpdb->query( "INSERT IGNORE INTO {$wpdb->bc_import_queue} ( bc_id, listing_id, date_modified, import_action, date_created, product_data, listing_data ) VALUES $values" );
		}

		do_action( 'bigcommerce/import/fetched_ids', $count, $listings_response );

		$next = $this->extract_next_from_response( $listings_response );
		if ( $next ) {
			do_action( 'bigcommerce/log', Error_Log::DEBUG, __( 'Ready for next page of products', 'bigcommerce' ), [
				'next' => $next,
			] );
			$this->set_next( $next );
		} else {
			$status->set_status( Status::FETCHED_PRODUCT_IDS );
			$this->clear_state();
		}
	}

	/**
	 * @param ListingCollectionResponse $response
	 *
	 * @return int The value for the parameter to use for the next page of results
	 */
	private function extract_next_from_response( ListingCollectionResponse $response ) {
		$next_args = $response->getMeta()->getPagination()->getLinks()->getNext();
		if ( empty( $next_args ) ) {
			return 0;
		}
		$next_args = ltrim( $next_args, '?' );
		parse_str( $next_args, $args );
		// Eventually, the API should change to support the page parameter. Until then, use after.
		if ( empty( $args[ 'after' ] ) ) {
			return 0;
		}

		return (int) $args[ 'after' ];
	}

	private function get_next() {
		$state = $this->get_state();
		if ( ! array_key_exists( 'next', $state ) ) {
			return 0;
		}

		return $state[ 'next' ];
	}

	private function set_next( $next ) {
		$state           = $this->get_state();
		$state[ 'next' ] = (int) $next;
		$this->set_state( $state );
	}

	private function get_state() {
		$state = $this->get_option( self::STATE_OPTION, [] );
		if ( ! is_array( $state ) ) {
			return [];
		}

		return $state;
	}

	private function set_state( array $state ) {
		$this->update_option( self::STATE_OPTION, $state, false );
	}

	private function clear_state() {
		$this->delete_option( self::STATE_OPTION );
	}
}