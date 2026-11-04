<?php


namespace BigCommerce\Import\Processors;

use BigCommerce\Api\v3\Api\CatalogApi;
use BigCommerce\Api\v3\Api\ChannelsApi;
use BigCommerce\Api\v3\ApiException;
use BigCommerce\Api\v3\Model\Listing;
use BigCommerce\Api\v3\Model\ListingVariant;
use BigCommerce\Api\v3\Model\Product;
use BigCommerce\Api\v3\Model\Variant;
use BigCommerce\Api\v3\Model\UpdateListingRequest;
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
 * Class Channel_Initializer
 *
 * Populates an empty channel with the full product catalog
 */
class Channel_Initializer implements Import_Processor {
	use No_Cache_Options;

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
	 * @var \WP_Term
	 */
	private $channel_term;

	/**
	 * Channel_Initializer constructor.
	 *
	 * @param ChannelsApi $channels
	 * @param CatalogApi  $catalog
	 * @param \WP_Term    $channel_term
	 * @param int         $limit Number of product IDs to fetch per request
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
	 * Build a request that reconciles an already-linked listing's state with the
	 * product's current visibility, or null when the stored listing already
	 * matches (so no needless write is sent).
	 *
	 * The group state follows the product's storefront visibility. Each variant
	 * state follows visibility too, then the variant's own purchasing flag: a
	 * variant is disabled when the product is hidden OR that variant is not
	 * purchasable, otherwise active — the same rule the create path uses, so an
	 * existing listing ends up identical to a freshly created one.
	 *
	 * The stored listing's variants are iterated (not the catalog product's), so
	 * the listing's variant membership and each variant's external_id are
	 * preserved and no variants are added that aren't already on the listing. The
	 * stored name/description/external_id are passed through unchanged so a
	 * reconcile never clobbers values Channel_Sync pushed.
	 *
	 * @param Product $product    The catalog product being imported.
	 * @param mixed   $stored     The decoded PRODUCT_LISTING_MAP entry for this listing.
	 * @param int     $channel_id The BigCommerce channel id.
	 *
	 * @return UpdateListingRequest|null
	 */
	private function build_reconcile_request( Product $product, $stored, $channel_id ) {
		if ( ! is_object( $stored ) || empty( $stored->listing_id ) ) {
			return null;
		}

		$is_visible  = (bool) $product->getIsVisible();
		$group_state = $is_visible ? 'active' : 'disabled';

		// Per-variant purchasing flags from the catalog, keyed by variant id.
		$purchasing = [];
		foreach ( (array) $product->getVariants() as $variant ) {
			$purchasing[ (int) $variant->getId() ] = (bool) $variant->getPurchasingDisabled();
		}

		$stored_variants = ( isset( $stored->variants ) && is_array( $stored->variants ) ) ? $stored->variants : [];
		$variants        = [];
		$variant_changed = false;

		foreach ( $stored_variants as $stored_variant ) {
			$variant_id = (int) ( $stored_variant->variant_id ?? 0 );
			$current    = $stored_variant->state ?? null;

			if ( array_key_exists( $variant_id, $purchasing ) ) {
				$variant_state = ( ! $is_visible || $purchasing[ $variant_id ] ) ? 'disabled' : 'active';
			} else {
				// Variant not in the catalog response; leave its state untouched.
				$variant_state = $current;
			}

			if ( $variant_state !== $current ) {
				$variant_changed = true;
			}

			$variants[] = new ListingVariant( [
				'product_id'  => (int) $product->getId(),
				'variant_id'  => $variant_id,
				'external_id' => $stored_variant->external_id ?? null,
				'state'       => $variant_state,
			] );
		}

		$group_changed = ( $stored->state ?? null ) !== $group_state;

		if ( ! $group_changed && ! $variant_changed ) {
			return null;
		}

		return new UpdateListingRequest( [
			'channel_id'  => $channel_id,
			'listing_id'  => (int) $stored->listing_id,
			'product_id'  => (int) $product->getId(),
			'state'       => $group_state,
			'name'        => $stored->name ?? null,
			'description' => $stored->description ?? null,
			'external_id' => $stored->external_id ?? null,
			'variants'    => $variants,
		] );
	}

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

		// Listing updates for products already linked to this channel whose listing
		// state no longer matches the product's visibility. Keeps the channel
		// listing in sync when visibility is toggled after the listing already
		// exists, instead of leaving it stuck at its creation-time state.
		$update_requests = [];

		$listing_requests = array_values( array_filter( array_map( function ( Product $product ) use ( $channel_id, $id_map, $import_type, $listing_ids, &$update_requests ) {
			// Already linked to this channel: reconcile the existing listing's
			// state with the product's current visibility instead of re-creating
			// it. Handled before the partial-import early return below so that a
			// partial import ("Import Now") also pushes visibility changes.
			if ( array_key_exists( $product->getId(), $id_map ) && array_key_exists( $this->channel_term->term_id, $id_map[ $product->getId() ] ) ) {
				$stored  = json_decode( (string) $id_map[ $product->getId() ][ $this->channel_term->term_id ] );
				$request = $this->build_reconcile_request( $product, $stored, (int) $channel_id );
				if ( $request ) {
					$update_requests[] = $request;
				}

				return false;
			}

			/**
			 * We will skip existing products listing creation for partial import
			 */
			if ( $import_type === Import_Type::IMPORT_TYPE_PARTIAL && $this->is_existing_product( $product, $channel_id ) ) {
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
						// A variant is disabled when the product is hidden OR the
						// variant itself is not purchasable. Deriving the group
						// state from is_visible while deriving the variant state
						// only from purchasing_disabled let a hidden-but-purchasable
						// product be listed with a `disabled` parent and `active`
						// variants; gating on visibility too keeps the group and
						// its variants consistent.
						'state'      => ( ! $product->getIsVisible() || $variant->getPurchasingDisabled() ) ? 'disabled' : 'active',
					] );
				}, $product->getVariants() ),
			] );
		}, $response->getData() ) ) );


		// Reconcile existing listings first, independently of new-listing creation,
		// so a create failure can't block visibility updates to already-linked
		// products.
		if ( ! empty( $update_requests ) ) {
			do_action( 'bigcommerce/log', Error_Log::DEBUG, __( 'Reconciling listing states with product visibility', 'bigcommerce' ), [
				'count' => count( $update_requests ),
			] );
			try {
				$this->channels->updateChannelListings( $channel_id, $update_requests );
			} catch ( ApiException $e ) {
				// A reconcile failure must not fail the whole import: firing
				// bigcommerce/import/error would trash the queue, delete the
				// listing map and mark the import as failed. Log a warning and
				// continue; Listing_Fetcher refreshes the stored state on the
				// next run, so the reconcile is retried then.
				do_action( 'bigcommerce/log', Error_Log::WARNING, sprintf( __( 'Could not reconcile listing states with product visibility: %s', 'bigcommerce' ), $e->getMessage() ), [
					'response' => $e->getResponseBody(),
					'headers'  => $e->getResponseHeaders(),
				] );
			}
		}

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
