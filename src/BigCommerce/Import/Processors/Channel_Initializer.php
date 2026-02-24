<?php


namespace BigCommerce\Import\Processors;

use BigCommerce\Api\v3\Api\CatalogApi;
use BigCommerce\Api\v3\Api\ChannelsApi;
use BigCommerce\Api\v3\ApiException;
use BigCommerce\Api\v3\Model\Listing;
use BigCommerce\Api\v3\Model\ListingVariant;
use BigCommerce\Api\v3\Model\Product;
use BigCommerce\Api\v3\Model\Variant;
use BigCommerce\Api\v3\ObjectSerializer;
use BigCommerce\Exceptions\Product_Not_Found_Exception;
use BigCommerce\Import\Import_Type;
use BigCommerce\Import\No_Cache_Options;
use BigCommerce\Import\Runner\Status;
use BigCommerce\Logging\Error_Log;
use BigCommerce\Settings\Sections\Import;
use BigCommerce\Taxonomies\Channel\Channel;
use \BigCommerce\Post_Types\Product\Product as Product_Post_Type;

/**
 * Initializes a channel by linking it to the full product catalog.
 * This process involves checking for existing products, skipping already linked products,
 * retrieving product listings, and adding new products to the channel.
 * The class also manages pagination for large product catalogs, ensuring that products
 * are processed in batches based on the set limit.
 *
 * @package BigCommerce\Import\Processors
 */
class Channel_Initializer implements Import_Processor {
	use No_Cache_Options;

	/**
	 * The option key used to store the state of the BigCommerce channel initialization.
	 * This key is used to track the progress of importing products into the specified channel.
	 *
	 * @var string
	 */
	const STATE_OPTION = 'bigcommerce_import_channel_init_state';

	/**
	 * The ChannelsApi instance used to interact with the BigCommerce Channels API.
	 * This API allows for managing channel-specific product listings and other channel-related operations.
	 *
	 * @var ChannelsApi
	 */
	private $channels;

	/**
	 * The CatalogApi instance used to interact with the BigCommerce Catalog API.
	 * This API allows for retrieving product details, variants, and managing the product catalog.
	 *
	 * @var CatalogApi
	 */
	private $catalog;

	/**
	 * The maximum number of product IDs to fetch per request.
	 * This value is used to limit the number of products retrieved during product imports.
	 *
	 * @var int
	 */
	private $limit;

	/**
	 * The WordPress term associated with the channel.
	 * This term represents the channel in the WordPress taxonomy and is used for associating products with the specific channel.
	 *
	 * @var \WP_Term
	 */
	private $channel_term;

	/**
	 * Channel_Initializer constructor.
	 *
	 * Initializes the Channel_Initializer class with the given parameters.
	 *
	 * @param ChannelsApi $channels     The API object for managing channels.
	 * @param CatalogApi  $catalog      The API object for managing the product catalog.
	 * @param \WP_Term    $channel_term The WordPress term representing the channel.
	 * @param int         $limit        The number of products to process per request (default 100).
	 */
	public function __construct( ChannelsApi $channels, CatalogApi $catalog, \WP_Term $channel_term, $limit = 100 ) {
		$this->channels     = $channels;
		$this->catalog      = $catalog;
		$this->channel_term = $channel_term;
		$this->limit        = $limit;
	}

	/**
	 * Check if the product exists
	 *
	 * @param $product
	 * @param $channel_id
	 *
	 * @return bool
	 */
	private function is_existing_product( $product, $channel_id ): bool {
		try {
			$query_args = [ 'post_status' => [ 'publish', 'draft' ] ];
			Product_Post_Type::by_product_id( $product->getId(), $this->channel_term, $query_args );

			do_action( 'bigcommerce/log', Error_Log::DEBUG, __( 'Product entity exists. Skipping.', 'bigcommerce' ), [
					'product_id' => $product->getId(),
					'channel_id' => $channel_id,
			] );

			return true;
		} catch ( Product_Not_Found_Exception $e ) {
			return false;
		}
	}

	/**
	 * Executes the channel initialization process.
	 * This includes retrieving products, checking their status, and adding them to the channel.
	 * Handles pagination for large product catalogs.
	 */
	public function run() {

		$status = new Status();
		$status->set_status( Status::INITIALIZING_CHANNEL . '-' . $this->channel_term->term_id );

		$channel_id = get_term_meta( $this->channel_term->term_id, Channel::CHANNEL_ID, true );
		if ( empty( $channel_id ) ) {
			do_action( 'bigcommerce/import/error', __( 'Channel ID is not set. Product import canceled.', 'bigcommerce' ) );

			return;
		}

		$listing_ids = [];
		// Get the listings for this channel.
		if ( $this->multichannel_sync_channel_listings() ) {
			try {
				$listings    = $this->channels->listChannelListings( $channel_id );
				$listing_ids = array_map( function ( Listing $listing ) {
					return $listing->getProductId();
				}, $listings->getData() );
			} catch ( ApiException $e ) {
				do_action( 'bigcommerce/import/error', $e->getMessage(), [
					'response' => $e->getResponseBody(),
					'headers'  => $e->getResponseHeaders(),
				] );
				do_action( 'bigcommerce/log', Error_Log::DEBUG, $e->getTraceAsString(), [] );

				return;
			}
		}

		$page = $this->get_page();
		if ( empty( $page ) ) {
			if ( ! get_option( Import::OPTION_NEW_PRODUCTS, 1 ) ) {
				do_action( 'bigcommerce/log', Error_Log::DEBUG, __( 'Skipping channel initialization due to settings', 'bigcommerce' ), [] );
				$status->set_status( Status::INITIALIZED_CHANNEL . '-' . $this->channel_term->term_id );
				$this->clear_state();

				return;
			}
			$page = 1;
		}

		do_action( 'bigcommerce/log', Error_Log::DEBUG, __( 'Retrieving products', 'bigcommerce' ), [
			'limit' => $this->limit,
			'page'  => $page,
		] );

		try {
			$response = $this->catalog->getProducts( [
				'include'        => [ 'variants' ],
				'include_fields' => [ 'id', 'name', 'description', 'is_visible' ],
				'page'           => $page,
				'limit'          => $this->limit,
			] );
		} catch ( ApiException $e ) {
			do_action( 'bigcommerce/import/error', $e->getMessage(), [
				'response' => $e->getResponseBody(),
				'headers'  => $e->getResponseHeaders(),
			] );
			do_action( 'bigcommerce/log', Error_Log::DEBUG, $e->getTraceAsString(), [] );

			return;
		}

		$id_map = $this->get_option( Listing_Fetcher::PRODUCT_LISTING_MAP, [] ) ?: [];

		$import_type = get_option( Import_Type::IMPORT_TYPE );

		$listing_requests = array_values( array_filter( array_map( function ( Product $product ) use ( $channel_id, $id_map, $import_type, $listing_ids ) {
			/**
			 * We will skip existing products listing creation for partial import
			 */
			if ( $import_type === Import_Type::IMPORT_TYPE_PARTIAL && $this->is_existing_product( $product, $channel_id ) ) {
				return false;
			}

			if ( array_key_exists( $product->getId(), $id_map ) && array_key_exists( $this->channel_term->term_id, $id_map[ $product->getId() ] ) ) {
				do_action( 'bigcommerce/log', Error_Log::DEBUG, __( 'Product already linked to channel. Skipping.', 'bigcommerce' ), [
					'product_id' => $product->getId(),
					'channel_id' => $channel_id,
				] );

				return false;
			}

			// Return if this product should not be synced to this channel.
			if ( $this->multichannel_sync_channel_listings() && ! in_array( $product->getId(), $listing_ids ) ) {
				do_action( 'bigcommerce/log', Error_Log::DEBUG, __( 'Product does not belong in this channel. Skipping.', 'bigcommerce' ), [
					'product_id' => $product->getId(),
					'channel_id' => $channel_id,
				] );

				return false;
			}

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
		}, $response->getData() ) ) );


		if ( ! empty( $listing_requests ) ) {
			do_action( 'bigcommerce/log', Error_Log::DEBUG, __( 'Adding products to channel', 'bigcommerce' ), [
				'count' => count( $listing_requests ),
			] );
			try {
				$create_response = $this->channels->createChannelListings( $channel_id, $listing_requests );
				foreach ( $create_response->getData() as $listing ) {
					$data = ObjectSerializer::sanitizeForSerialization( $listing );
					$id_map[ (int) $listing->getProductId() ][ $this->channel_term->term_id ] = wp_json_encode( $data );
				}
				$this->update_option( Listing_Fetcher::PRODUCT_LISTING_MAP, $id_map, false );
			} catch ( ApiException $e ) {
				do_action( 'bigcommerce/import/error', $e->getMessage(), [
					'response' => $e->getResponseBody(),
					'headers'  => $e->getResponseHeaders(),
				] );
				do_action( 'bigcommerce/log', Error_Log::DEBUG, $e->getTraceAsString(), [] );

				return;
			}
		}

		$total_pages = $response->getMeta()->getPagination()->getTotalPages();
		if ( $total_pages > $page ) {
			do_action( 'bigcommerce/log', Error_Log::DEBUG, __( 'Channel initialization ready for next page of products', 'bigcommerce' ), [
				'next' => $page + 1,
			] );
			$this->set_page( $page + 1 );
		} else {
			$status->set_status( Status::INITIALIZED_CHANNEL . '-' . $this->channel_term->term_id );
			$this->clear_state();
		}
	}

	private function get_page() {
		$state = $this->get_state();
		if ( ! array_key_exists( 'page', $state ) ) {
			return 0;
		}

		return $state['page'];
	}

	private function set_page( $page ) {
		$state         = $this->get_state();
		$state['page'] = (int) $page;
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
		return sprintf( '%s-%d', self::STATE_OPTION, $this->channel_term->term_id );
	}

	private function multichannel_sync_channel_listings() {
		return ( Channel::multichannel_enabled() && ! Channel::multichannel_sync_to_all_channels() );
	}
}
