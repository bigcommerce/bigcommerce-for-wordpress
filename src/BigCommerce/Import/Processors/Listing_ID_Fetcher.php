<?php


namespace BigCommerce\Import\Processors;


use BigCommerce\Api\v3\Api\ChannelsApi;
use BigCommerce\Api\v3\ApiException;
use BigCommerce\Api\v3\Api\CatalogApi;
use BigCommerce\Api\v3\Model\Listing;
use BigCommerce\Api\v3\Model\ListingCollectionResponse;
use BigCommerce\Import\No_Cache_Options;
use BigCommerce\Import\Runner\Status;
use BigCommerce\Logging\Error_Log;
use BigCommerce\Settings\Sections\Channels;

class Listing_ID_Fetcher implements Import_Processor {
	use No_Cache_Options;

	const STATE_OPTION        = 'bigcommerce_import_listing_id_fetcher_state';
	const PRODUCT_LISTING_MAP = 'bigcommerce_product_listing_map';

	/**
	 * @var ChannelsApi
	 */
	private $channels;

	/**
	 * @var int
	 */
	private $limit;

	/**
	 * Listing_ID_Fetcher constructor.
	 *
	 * @param ChannelsApi $channels The Channels API connection to use for the import
	 * @param int         $limit    Number of listing IDs to fetch per request
	 */
	public function __construct( ChannelsApi $channels, $limit = 100 ) {
		$this->limit    = $limit;
		$this->channels = $channels;
	}

	public function run() {
		$status = new Status();
		$status->set_status( Status::FETCHING_LISTING_IDS );

		$channel_id = get_option( Channels::CHANNEL_ID, 0 );
		if ( empty( $channel_id ) ) {
			do_action( 'bigcommerce/import/error', __( 'Channel ID is not set. Product import canceled.', 'bigcommerce' ) );

			return;
		}

		$next = $this->get_next();
		if ( empty( $next ) ) {
			$this->update_option( self::PRODUCT_LISTING_MAP, [], false );
		}
		do_action( 'bigcommerce/log', Error_Log::DEBUG, __( 'Retrieving listings', 'bigcommerce' ), [
			'limit' => $this->limit,
			'after' => $next ?: null,
		] );

		try {
			$response = $this->channels->listChannelListings( $channel_id, [
				'limit' => $this->limit,
				'after' => $next ?: null,
			] );
		} catch ( ApiException $e ) {
			do_action( 'bigcommerce/import/error', $e->getMessage(), [
				'response' => $e->getResponseBody(),
				'headers'  => $e->getResponseHeaders(),
			] );

			return;
		}

		$id_map = $this->get_option( self::PRODUCT_LISTING_MAP, [] ) ?: [];
		foreach ( $response->getData() as $listing ) {
			$id_map[ (int) $listing->getProductId() ] = (int) $listing->getListingId();
		}
		$this->update_option( self::PRODUCT_LISTING_MAP, $id_map, false );

		$next = $this->extract_next_from_response( $response );
		if ( $next ) {
			do_action( 'bigcommerce/log', Error_Log::DEBUG, __( 'Ready for next page of listings', 'bigcommerce' ), [
				'next' => $next,
			] );
			$this->set_next( $next );
		} else {
			$status->set_status( Status::FETCHED_LISTING_IDS );
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