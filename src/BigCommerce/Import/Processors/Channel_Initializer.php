<?php


namespace BigCommerce\Import\Processors;

use BigCommerce\Api\v3\Api\CatalogApi;
use BigCommerce\Api\v3\Api\ChannelsApi;
use BigCommerce\Api\v3\ApiException;
use BigCommerce\Api\v3\Model\Listing;
use BigCommerce\Api\v3\Model\ListingVariant;
use BigCommerce\Api\v3\Model\Product;
use BigCommerce\Api\v3\Model\Variant;
use BigCommerce\Import\Runner\Status;
use BigCommerce\Settings\Sections\Channels;

/**
 * Class Channel_Initializer
 *
 * Populates an empty channel with the full product catalog
 */
class Channel_Initializer implements Import_Processor {
	const STATE_OPTION = 'bigcommerce_import_channel_init_state';

	/**
	 * @var ChannelsApi
	 */
	private $channels;

	/**
	 * @var CatalogApi
	 */
	private $catalog;

	/**
	 * @var int
	 */
	private $limit;

	/**
	 * Product_ID_Fetcher constructor.
	 *
	 * @param ChannelsApi $channels
	 * @param CatalogApi  $catalog
	 * @param int         $limit Number of product IDs to fetch per request
	 */
	public function __construct( ChannelsApi $channels, CatalogApi $catalog, $limit = 100 ) {
		$this->channels = $channels;
		$this->catalog  = $catalog;
		$this->limit    = $limit;
	}

	public function run() {

		$status = new Status();
		$status->set_status( Status::INITIALIZING_CHANNEL );

		$channel_id = (int) get_option( Channels::CHANNEL_ID, 0 );
		if ( empty( $channel_id ) ) {
			do_action( 'bigcommerce/import/error', __( 'Channel ID is not set. Product import canceled.', 'bigcommerce' ) );

			return;
		}

		$page = $this->get_page();
		if ( empty( $page ) ) {
			if ( $this->channel_has_listings( $channel_id ) ) {
				$status->set_status( Status::INITIALIZED_CHANNEL );
				$this->clear_state();

				return;
			} else {
				$page = 1;
			}
		}


		try {
			$response = $this->catalog->getProducts(
				null, // $id
				null, // $name
				null, // $sku
				null, // $upc
				null, // $price
				null, // $weight
				null, // $condition
				null, // $brand_id
				null, // $date_modified
				null, // $date_last_imported
				null, // $is_visible
				null, // $is_featured
				null, // $is_free_shipping
				null, // $inventory_level
				null, // $inventory_low
				null, // $out_of_stock
				null, // $total_sold
				null, // $type
				null, // $categories
				null, // $keyword
				null, // $keyword_context
				null, // $status
				[ 'variants' ], // $include
				[ 'id', 'name', 'description', 'is_visible' ], // $include_fields
				null, // $exclude_fields
				null, // $availability
				null, // $price_list_id
				$page, // $page
				$this->limit, // $limit
				null, // $direction
				null  // $sort
			);
		} catch ( ApiException $e ) {
			do_action( 'bigcommerce/import/error', $e->getMessage(), [
				'response' => $e->getResponseBody(),
				'headers'  => $e->getResponseHeaders(),
			] );

			return;
		}

		$listing_requests = array_map( function ( Product $product ) use ( $channel_id ) {
			return new Listing( [
				'channel_id' => (int) $channel_id,
				'product_id' => (int) $product->getId(),
				'state'      => $product->getIsVisible() ? 'active' : 'disabled',
				//'name'        => $product->getName(), // leave off to inherit from product
				//'description' => $product->getDescription(), // leave off to inherit from product
				'variants'   => array_map( function ( Variant $variant ) use ( $product ) {
					return new ListingVariant( [
						'product_id' => (int) $product->getId(),
						'variant_id' => (int) $variant->getId(),
						'state'      => $variant->getPurchasingDisabled() ? 'disabled' : 'active',
					] );
				}, $product->getVariants() ),
			] );
		}, $response->getData() );


		try {
			$response = $this->channels->createChannelListings( $channel_id, $listing_requests );
		} catch ( ApiException $e ) {
			do_action( 'bigcommerce/import/error', $e->getMessage(), [
				'response' => $e->getResponseBody(),
				'headers'  => $e->getResponseHeaders(),
			] );

			return;
		}

		$total_pages = $response->getMeta()->getPagination()->getTotalPages();
		if ( $total_pages > $page ) {
			$this->set_page( $page + 1 );
		} else {
			$status->set_status( Status::INITIALIZED_CHANNEL );
			$this->clear_state();
		}
	}

	/**
	 * Determine if the given channel has any listings
	 *
	 * @param int $channel_id
	 *
	 * @return bool
	 */
	private function channel_has_listings( $channel_id ) {
		try {
			$response = $this->channels->listChannelListings( $channel_id, 1 );

			return count( $response->getData() ) > 0;
		} catch ( ApiException $e ) {
			return false;
		}
	}


	private function get_page() {
		$state = $this->get_state();
		if ( ! array_key_exists( 'page', $state ) ) {
			return 0;
		}

		return $state[ 'page' ];
	}

	private function set_page( $page ) {
		$state           = $this->get_state();
		$state[ 'page' ] = (int) $page;
		$this->set_state( $state );
	}

	private function get_state() {
		$state = get_option( self::STATE_OPTION, [] );
		if ( ! is_array( $state ) ) {
			return [];
		}

		return $state;
	}

	private function set_state( array $state ) {
		update_option( self::STATE_OPTION, $state, false );
	}

	private function clear_state() {
		delete_option( self::STATE_OPTION );
	}
}