<?php

namespace BigCommerce\Webhooks\Product;

use BigCommerce\Logging\Error_Log;
use BigCommerce\Schema\Queue_Table;

class Channel_Updater extends Channels_Manager {

	public function handle_request( $channel_id ) {
		$channel = $this->get_channel( $channel_id );

		if ( empty( $channel ) ) {
			do_action( 'bigcommerce/log', Error_Log::INFO, __( 'Requested channel does not exist', 'bigcommerce' ), [
				'channel_id' => $channel_id,
			], 'webhooks' );

			return;
		}

		// Retrieve remote channel data
		try {
			$channel_remote = $this->channels_api->getChannel( $channel_id )->getData();
		} catch ( \Exception $exception ) {
			do_action( 'bigcommerce/log', Error_Log::ERROR, __( 'Could not retrieve the channel', 'bigcommerce' ), [
				'channel_id' => $channel_id,
			], 'webhooks' );

			return;
		}

		$args = [
			'channel' => $channel_remote->getId(),
			'status'  => $channel_remote->getStatus(),
			'term'    => $channel
		];

		global $wpdb;
		$table = $wpdb->prefix . Queue_Table::NAME;

		// Save data for further processing
		$result = $wpdb->query(
			$wpdb->prepare(
				"INSERT INTO `$table` (handler, args) VALUES (%s, %s)",
				'Bigcommerce\Manager\Channel_Update_Task',
				json_encode( $args )
			)
		);

		if ( ! $result || is_wp_error( $result ) ) {
			do_action( 'bigcommerce/log', Error_Log::ERROR, __( 'Could not store task to queue', 'bigcommerce' ), [
				'channel_id' => $channel_id,
				'args'       => $args
			], 'webhooks' );

			return;
		}

		do_action( 'bigcommerce/log', Error_Log::INFO, __( 'Task has been saved', 'bigcommerce' ), [
			'channel_id' => $channel_id,
			'args'       => $args
		], 'webhooks' );
	}

}
