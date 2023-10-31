<?php


namespace BigCommerce\Post_Types\Product;

use BigCommerce\Api\v3\Api\ChannelsApi;
use BigCommerce\Api\v3\ApiException;
use BigCommerce\Api\v3\Model\Listing;
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

	public function __construct( ChannelsApi $channels ) {
		$this->channels = $channels;
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

		$update_request = new UpdateListingRequest( [
			'channel_id'    => $channel_id,
			'listing_id'    => $listing_id,
			'product_id'    => (int) $listing->getProductId(),
			'state'         => $this->get_listing_state( $post_id, $listing ),
			'name'          => $this->get_listing_title( $post_id, $listing ),
			'description'   => $this->get_listing_description( $post_id, $listing ),
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