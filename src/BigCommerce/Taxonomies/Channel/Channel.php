<?php


namespace BigCommerce\Taxonomies\Channel;

use BigCommerce\Exceptions\Channel_Not_Found_Exception;

class Channel {
	const NAME = 'bigcommerce_channel';

	const CHANNEL_ID = 'bigcommerce_channel_id';
	const STATUS     = 'bigcommerce_channel_status';

	const STATUS_PRIMARY      = 'primary';
	const STATUS_CONNECTED    = 'connected';
	const STATUS_DISCONNECTED = 'disconnected';
	const STATUS_ORPHAN       = 'orphan';

	/**
	 * Get the site's multi-channel support status
	 *
	 * @return bool
	 */
	public static function multichannel_enabled() {
		/**
		 * Filter whether multi-channel support is enabled.
		 * Enabling this feature allows site owners to
		 * connect to multiple channels and switch between
		 * them based on arbitrary criteria.
		 *
		 * @see bigcommerce/channel/current
		 *
		 * @param bool $enabled Whether multi-channel is enabled. Defaults to false.
		 */
		return (bool) apply_filters( 'bigcommerce/channels/enable-multi-channel', false );
	}

	/**
	 * @param int $channel_id
	 *
	 * @return \WP_Term
	 */
	public static function find_by_id( $channel_id ) {
		$channels = get_terms( [
			'taxonomy'   => self::NAME,
			'hide_empty' => false,
			'meta_query' => [
				[
					'key'   => self::CHANNEL_ID,
					'value' => $channel_id,
				],
			],
			'number' => 1,
		] );
		if ( empty( $channels ) ) {
			throw new Channel_Not_Found_Exception( sprintf( __( 'No channel found matching channel ID %d'), $channel_id ) );
		}
		return reset( $channels );
	}
}