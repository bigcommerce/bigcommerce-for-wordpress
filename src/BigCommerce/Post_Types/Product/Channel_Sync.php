<?php


namespace BigCommerce\Post_Types\Product;

use BigCommerce\Api\v3\Api\CatalogApi;
use BigCommerce\Api\v3\Api\ChannelsApi;
use BigCommerce\Api\v3\ApiException;
use BigCommerce\Api\v3\Model\Listing;
use BigCommerce\Api\v3\Model\ListingVariant;
use BigCommerce\Api\v3\Model\UpdateListingRequest;
use BigCommerce\Settings\Sections\Channels;

/**
 * Class Channel_Sync
 *
 * Synchronizes changes to the Product state with the BigCommerce Channel
 */
class Channel_Sync {

	/**
	 * @var ChannelsApi
	 */
	private $channels;

	/**
	 * @var CatalogApi
	 */
	private $catalog;

	public function __construct( ChannelsApi $channels, CatalogApi $catalog ) {
		$this->channels = $channels;
		$this->catalog  = $catalog;
	}

	/**
	 * Listen for updates to product posts to trigger an update
	 * to the BigCommerce channel listing
	 *
	 * @param int      $post_id
	 * @param \WP_Post $post
	 *
	 * @return void
	 * @action save_post
	 */
	public function post_updated( $post_id, $post ) {
		if ( Product::NAME !== $post->post_type ) {
			return;
		}

		/**
		 * Filter whether updates to the post should be pushed up to the
		 * BigCommerce channel listing.
		 *
		 * @param bool $update  Whether the update should be pushed
		 * @param int  $post_id The ID of the post being updated
		 */
		if ( ! apply_filters( 'bigcommerce/channel/listing/should_update', true, $post_id ) ) {
			return;
		}

		$product = new Product( $post_id );
		$channel_id = $product->get_channel_id();
		$listing_id = $product->get_listing_id();
		if ( empty( $channel_id ) || empty( $listing_id ) ) {
			return;
		}

		try {
			$response = $this->channels->getChannelListing( $channel_id, $listing_id );
			$listing  = $response->getData();
		} catch ( ApiException $e ) {
			/**
			 * Error triggered when fetching a listing fails
			 *
			 * @param int $channel_id
			 * @param int $listing_id
			 * @param ApiException $e
			 */
			do_action( 'bigcommerce/channel/error/could_not_fetch_listing', $channel_id, $listing_id, $e );

			return;
		}

		$state = $this->get_listing_state( $post_id, $listing );

		$update_request = new UpdateListingRequest( [
			'channel_id'    => $channel_id,
			'listing_id'    => $listing_id,
			'product_id'    => (int) $listing->getProductId(),
			'state'         => $state,
			'name'          => $this->get_listing_title( $post_id, $listing ),
			'description'   => $this->get_listing_description( $post_id, $listing ),
			'external_id'   => $listing->getExternalId(),
			'variants'      => $this->sync_variant_states( $state, $listing ),
		] );

		try {
			$this->channels->updateChannelListings( $channel_id, [ $update_request ] );
		} catch ( ApiException $e ) {
			/**
			 * Error triggered when updating a listing fails
			 *
			 * @param int $channel_id
			 * @param int $listing_id
			 * @param ApiException $e
			 */
			do_action( 'bigcommerce/channel/error/could_not_update_listing', $channel_id, $listing_id, $e );

			return;
		}

		try {
			$response = $this->channels->getChannelListing( $channel_id, $listing_id );
			$listing  = $response->getData();
			$product->update_listing_data( $listing );
		} catch ( ApiException $e ) {
			/**
			 * Error triggered when fetching a listing fails
			 *
			 * @param int $channel_id
			 * @param int $listing_id
			 * @param ApiException $e
			 */
			do_action( 'bigcommerce/channel/error/could_not_fetch_listing', $channel_id, $listing_id, $e );

			return;
		}
	}

	/**
	 * @param int     $post_id
	 * @param Listing $listing
	 *
	 * @return string
	 */
	private function get_listing_state( $post_id, Listing $listing ) {
		switch ( get_post_status( $post_id ) ) {
			case 'trash':
				$state = 'pending_delete';
				break;
			case 'pending':
				$state = 'pending';
				break;
			case 'draft':
				$state = 'disabled';
				break;
			case 'private':
				$state = 'unknown'; // TODO: API needs a better state for this
				break;
			default:
				$state = 'active';
				break;
		}

		/**
		 * Filter the state to set on the channel listing for the product
		 *
		 * @param string  $state   The listing state to set
		 * @param int     $post_id The ID of the product post in WordPress
		 * @param Listing $listing The listing from BigCommerce that will be updated
		 */
		return apply_filters( 'bigcommerce/channel/listing/state', $state, $post_id, $listing );
	}

	/**
	 * Keep variant listing states consistent with the parent listing state.
	 *
	 * When the parent is not `active`, force every variant to `disabled` so the
	 * whole listing is dark. When the parent is `active`, set each variant
	 * `active` except the ones the merchant disabled purchasing on in the
	 * catalog, which is the source of truth for per-variant purchasing state.
	 *
	 * @param string  $state   The state being set on the parent listing
	 * @param Listing $listing The listing fetched from BigCommerce
	 *
	 * @return ListingVariant[]
	 */
	private function sync_variant_states( $state, Listing $listing ) {
		$variants = $listing->getVariants();

		if ( 'active' !== $state ) {
			return array_map( function ( ListingVariant $variant ) {
				$variant->setState( 'disabled' );

				return $variant;
			}, $variants );
		}

		$purchasing = $this->get_variant_purchasing_map( (int) $listing->getProductId() );

		return array_map( function ( ListingVariant $variant ) use ( $purchasing ) {
			$disabled = ! empty( $purchasing[ (int) $variant->getVariantId() ] );
			$variant->setState( $disabled ? 'disabled' : 'active' );

			return $variant;
		}, $variants );
	}

	/**
	 * Map of catalog variant id => purchasing_disabled for a product. On an API
	 * failure returns an empty map, so variants default to `active` for a visible
	 * product rather than staying stuck `disabled`.
	 *
	 * @param int $product_id The BigCommerce catalog product id
	 *
	 * @return array<int,bool>
	 */
	private function get_variant_purchasing_map( $product_id ) {
		$map = [];

		try {
			$variants = $this->catalog->getVariantsByProductId( $product_id )->getData();
		} catch ( ApiException $e ) {
			/**
			 * Error triggered when fetching catalog variants fails
			 *
			 * @param int          $product_id
			 * @param ApiException $e
			 */
			do_action( 'bigcommerce/channel/error/could_not_fetch_variants', $product_id, $e );

			return $map;
		}

		foreach ( $variants as $variant ) {
			$map[ (int) $variant->getId() ] = (bool) $variant->getPurchasingDisabled();
		}

		return $map;
	}

	private function get_listing_title( $post_id, Listing $listing ) {
		/**
		 * Filter the title to set on the channel listing for the product
		 *
		 * @param string  $title   The listing title to set
		 * @param int     $post_id The ID of the product post in WordPress
		 * @param Listing $listing The listing from BigCommerce that will be updated
		 */
		return apply_filters( 'bigcommerce/channel/listing/title', get_post_field( 'post_title', $post_id ), $post_id, $listing );
	}

	private function get_listing_description( $post_id, Listing $listing ) {
		/**
		 * Filter the description to set on the channel listing for the product
		 *
		 * @param string  $title   The listing description to set
		 * @param int     $post_id The ID of the product post in WordPress
		 * @param Listing $listing The listing from BigCommerce that will be updated
		 */
		return apply_filters( 'bigcommerce/channel/listing/description', get_post_field( 'post_content', $post_id ), $post_id, $listing );
	}

	/**
	 * When a post is deleted, permanently mark it deleted in the channel
	 *
	 * @param int $post_id
	 *
	 * @return void
	 * @action before_delete_post 5
	 */
	public function post_deleted( $post_id ) {

		if ( Product::NAME !== get_post_type( $post_id ) ) {
			return;
		}

		/**
		 * Filter whether deleting the post should be pushed up to the
		 * BigCommerce channel listing.
		 *
		 * @param bool $update  Whether the update should be pushed
		 * @param int  $post_id The ID of the post being updated
		 */
		if ( ! apply_filters( 'bigcommerce/channel/listing/should_delete', true, $post_id ) ) {
			return;
		}

		$product = new Product( $post_id );
		$channel_id = $product->get_channel_id();
		$listing_id = $product->get_listing_id();
		if ( empty( $channel_id ) || empty( $listing_id ) ) {
			return;
		}

		try {
			$response = $this->channels->getChannelListing( $channel_id, $listing_id );
			$listing  = $response->getData();
		} catch ( ApiException $e ) {
			/**
			 * Error triggered when fetching a listing fails
			 *
			 * @param int $channel_id
			 * @param int $listing_id
			 * @param ApiException $e
			 */
			do_action( 'bigcommerce/channel/error/could_not_fetch_listing', $channel_id, $listing_id, $e );

			return;
		}

		$update_request = new UpdateListingRequest( [
			'channel_id'    => $channel_id,
			'listing_id'    => $listing_id,
			'product_id'    => $listing->getProductId(),
			'state'         => 'deleted',
			'name'          => $listing->getName(),
			'description'   => $listing->getDescription(),
			'external_id'   => $listing->getExternalId(),
			'variants'      => $listing->getVariants(),
		] );

		try {
			$this->channels->updateChannelListings( $channel_id, [ $update_request ] );
		} catch ( ApiException $e ) {
			/**
			 * Error triggered when updating a listing fails
			 *
			 * @param int $channel_id
			 * @param int $listing_id
			 * @param ApiException $e
			 */
			do_action( 'bigcommerce/channel/error/could_not_update_listing', $channel_id, $listing_id, $e );

			return;
		}
	}
}