<?php


namespace BigCommerce\Taxonomies\Channel;

use BigCommerce\Api\v3\Api\ChannelsApi;
use BigCommerce\Api\v3\ApiException;
use BigCommerce\Api\v3\Model\UpdateChannelRequest;
use BigCommerce\Logging\Error_Log;
use BigCommerce\Post_Types\Product\Product;
use BigCommerce\Settings\Sections\Channels;

/**
 * Class Channel_Synchronizer
 *
 * Synchronizes channel data in the WP taxonomy with
 * channels available via the API
 */
class Channel_Synchronizer {
	/** @var ChannelsApi */
	private $channels_api;

	public function __construct( ChannelsApi $channels_api ) {
		$this->channels_api = $channels_api;
	}

	/**
	 * Run an initial sync if channels have not been previously imported
	 *
	 * @return void
	 * @action bigcommerce/settings/before_form/page= . Settings_Screen::NAME
	 * @action bigcommerce/settings/before_form/page= . Connect_Channel_Screen::NAME
	 * @action bigcommerce/import/start
	 */
	public function initial_sync() {
		$terms = get_terms( [
			'taxonomy'   => Channel::NAME,
			'hide_empty' => false,
		] );
		if ( empty( $terms ) ) {
			$this->sync();
		}
	}

	/**
	 * If a Channel name is changed, push the change up to the API
	 *
	 * @param int $term_id
	 *
	 * @return void
	 * @action edited_ . Channel::NAME
	 */
	public function handle_name_change( $term_id ) {
		$term       = get_term( $term_id );
		$channel_id = get_term_meta( $term_id, Channel::CHANNEL_ID, true );
		if ( ! $channel_id ) {
			return; // can't sync if we don't know the identity
		}

		$request = new UpdateChannelRequest( [
			'type'     => 'storefront',
			'platform' => 'wordpress',
			'name'     => $term->name,
		] );
		try {
			$this->channels_api->updateChannel( $channel_id, $request );
		} catch ( ApiException $e ) {
			do_action( 'bigcommerce/log', Error_Log::WARNING, __( 'Error when updating channel name', 'bigcommerce' ), [
					'error'      => $e->getMessage(),
					'channel_id' => $channel_id,
					'name'       => $term->name,
				]
			);
			do_action( 'bigcommerce/log', Error_Log::DEBUG, $e->getTraceAsString(), [] );

			return;
		}
	}

	public function sync() {
		try {
			$channels = $this->fetch_channels_from_api();
		} catch ( ApiException $e ) {
			do_action( 'bigcommerce/log', Error_Log::ERROR, __( 'Unable to import channels', 'bigcommerce' ), $e->getMessage() );
			do_action( 'bigcommerce/log', Error_Log::DEBUG, $e->getTraceAsString(), [] );

			return;
		}

		/** @var \WP_Term[] $terms */
		$terms          = get_terms( [
			'taxonomy'   => Channel::NAME,
			'hide_empty' => false,
		] );
		$known_channels = [];

		// Synchronize previously imported channels
		foreach ( $terms as $term ) {
			$channel_id = get_term_meta( $term->term_id, Channel::CHANNEL_ID, true );
			if ( empty( $channel_id ) || ! array_key_exists( $channel_id, $channels ) ) {
				$this->mark_orphan( $term );
				continue;
			}

			$this->update_term( $term, $channels[ $channel_id ] );
			$known_channels[] = $channel_id;
		}

		// Add WP terms for all other channels
		foreach ( $channels as $channel_id => $channel ) {
			if ( in_array( $channel_id, $known_channels ) ) {
				continue; // already have a WP term
			}
			$this->insert_term( $channel );
		}
	}

	/**
	 * @return \BigCommerce\Api\v3\Model\Channel[]
	 * @throws \BigCommerce\Api\v3\ApiException
	 */
	private function fetch_channels_from_api() {
		/** @var \BigCommerce\Api\v3\Model\Channel[] $data */
		$data = $this->channels_api->listChannels()->getData();
		$data = array_filter( $data, function ( \BigCommerce\Api\v3\Model\Channel $channel ) {
			return $channel->getPlatform() === 'wordpress';
		} );

		$channels = [];
		foreach ( $data as $channel ) {
			$channels[ $channel->getId() ] = $channel;
		}

		return $channels;
	}

	/**
	 * A term does not have a corresponding channel in the
	 * API response. We have no idea what this term is. It shouldn't
	 * be here, and it was likely added by a 3rd-party plugin or
	 * left over from a previous installation that wasn't cleaned up.
	 *
	 * @param \WP_Term $term
	 */
	private function mark_orphan( \WP_Term $term ) {
		update_term_meta( $term->term_id, Channel::STATUS, Channel::STATUS_ORPHAN );
	}

	/**
	 * @param \WP_Term $term
	 * @param          $channel
	 *
	 * @return void
	 */
	private function update_term( \WP_Term $term, $channel ) {
		// Update the term name if the channel was renamed externally
		if ( $term->name !== $channel['name'] ) {
			wp_update_term( $term->term_id, $term->taxonomy, [
				'name' => $channel['name'],
			] );
		}

		// If a previous orphan now has a match (how did this happen?) mark it available
		if ( get_term_meta( $term->term_id, Channel::STATUS, true ) === Channel::STATUS_ORPHAN ) {
			update_term_meta( $term->term_id, Channel::STATUS, Channel::STATUS_DISCONNECTED );
		}
	}

	/**
	 * Create a new WP term for a channel
	 *
	 * @param \ArrayAccess|array $channel
	 *
	 * @return void
	 */
	private function insert_term( $channel ) {
		$term = wp_insert_term( $channel['name'], Channel::NAME );
		if ( is_wp_error( $term ) ) {
			do_action( 'bigcommerce/log', Error_Log::WARNING, __( 'Unable to import channel', 'bigcommerce' ), $term->get_error_message() );

			return;
		}
		update_term_meta( $term['term_id'], Channel::CHANNEL_ID, $channel['id'] );

		$primary_channel = get_option( Channels::CHANNEL_ID, 0 );
		if ( (int) $channel['id'] === (int) $primary_channel ) {
			$status = Channel::STATUS_PRIMARY;
		} else {
			$status = Channel::STATUS_DISCONNECTED;
		}
		update_term_meta( $term['term_id'], Channel::STATUS, $status );

		if ( $status === Channel::STATUS_PRIMARY ) {
			$this->bulk_assign_primary_channel( (int) $term['term_id'] );
		}
	}

	/**
	 * When creating the term for the primary channel,
	 * bulk assign it to any product that does not already
	 * have a channel assigned. This is to migrate sites
	 * that already have products in the DB before multi-
	 * channel was supported.
	 *
	 * @param int $term_id
	 *
	 * @return void
	 */
	private function bulk_assign_primary_channel( $term_id ) {
		/** @var \wpdb $wpdb */
		global $wpdb;
		$term     = get_term( $term_id, Channel::NAME );
		$post_ids = get_posts( [
			'fields'         => 'ids',
			'post_type'      => Product::NAME,
			'post_status'    => 'any',
			'posts_per_page' => - 1,
			'tax_query'      => [
				[
					'taxonomy' => Channel::NAME,
					'operator' => 'NOT EXISTS',
				],
			],
		] );
		if ( empty( $post_ids ) ) {
			return;
		}

		$inserts = array_map( function ( $post_id ) use ( $term ) {
			return sprintf( '( %d, %d )', $post_id, $term->term_taxonomy_id );
		}, $post_ids );

		$values = implode( ', ', $inserts );
		$wpdb->query( "INSERT IGNORE INTO {$wpdb->term_relationships} ( object_id, term_taxonomy_id ) VALUES $values" );

		// manually flush some caches after our direct DB query
		wp_update_term_count( $term->term_taxonomy_id, $term->taxonomy );
		wp_cache_delete( 'last_changed', 'terms' );
	}


}