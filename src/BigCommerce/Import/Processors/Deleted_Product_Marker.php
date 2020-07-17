<?php


namespace BigCommerce\Import\Processors;


use BigCommerce\Import\No_Cache_Options;
use BigCommerce\Import\Runner\Status;
use BigCommerce\Logging\Error_Log;
use BigCommerce\Post_Types\Product\Product;
use BigCommerce\Post_Types\Queue_Task\Queue_Task;
use BigCommerce\Taxonomies\Channel\Channel;
use BigCommerce\Taxonomies\Channel\Connections;

class Deleted_Product_Marker implements Import_Processor {
	use No_Cache_Options;

	public function run() {
		$status = new Status();
		$status->set_status( Status::MARKING_DELETED_PRODUCTS );

		$connections = new Connections();
		$connected   = $connections->active();

		$posts_not_in_connected_channels = get_posts( [
			'post_type'      => Product::NAME,
			'post_status'    => 'any',
			'posts_per_page' => - 1,
			'fields'         => 'ids',
			'tax_query'      => [
				[
					'taxonomy' => Channel::NAME,
					'field'    => 'term_taxonomy_id',
					'terms'    => wp_list_pluck( $connected, 'term_taxonomy_id' ),
					'operator' => 'NOT IN',
				],
			],
		] );

		$map               = $this->get_option( Listing_Fetcher::PRODUCT_LISTING_MAP, [] );
		$products_in_queue = array_keys( $map );

		$posts_not_in_queue = get_posts( [
			'post_type'      => Product::NAME,
			'post_status'    => 'any',
			'posts_per_page' => - 1,
			'fields'         => 'ids',
			'tax_query'      => [
				[
					'taxonomy' => Channel::NAME,
					'field'    => 'term_taxonomy_id',
					'terms'    => wp_list_pluck( $connected, 'term_taxonomy_id' ),
					'operator' => 'IN',
				],
			],
			'meta_query'     => [
				[
					'key'     => Product::BIGCOMMERCE_ID,
					'value'   => $products_in_queue,
					'compare' => 'NOT IN',
				],
			],
		] );

		$posts_to_delete = array_merge( $posts_not_in_queue, $posts_not_in_connected_channels );

		$count = 0;
		if ( ! empty( $posts_to_delete ) ) {
			do_action( 'bigcommerce/log', Error_Log::DEBUG, sprintf( __( 'Queuing %d products for deletion', 'bigcommerce' ), count( $posts_to_delete ) ), [
				'post_ids' => $posts_to_delete,
			] );

			wp_suspend_cache_invalidation( true );
			wp_defer_term_counting( true );
			wp_defer_comment_counting( true );

			foreach ( $posts_to_delete as $post_id ) {
				$task_id = wp_insert_post( [
					'post_type'    => Queue_Task::NAME,
					'post_status'  => 'delete',
					'post_content' => wp_json_encode( [ 'post_id' => $post_id ] ),
					'post_date'    => current_time( 'mysql' ),
					'post_name'    => sprintf( 'delete-post-%d', $post_id ),
					'post_title'   => sprintf( 'Delete post %d', $post_id ),
					'menu_order'   => 0,
				], true );
				if ( is_wp_error( $task_id ) ) {
					do_action( 'bigcommerce/log', Error_Log::WARNING, __( 'Error adding deletion to import queue', 'bigcommerce' ), [
						'post_id' => $post_id,
						'error'   => $task_id->get_error_message(),
					] );
				} else {
					$count ++;
				}
			}

			wp_suspend_cache_invalidation( false );
			wp_defer_term_counting( false );
			wp_defer_comment_counting( false );
		}

		/**
		 * Triggered when a batch of posts have been marked for deletion, due
		 * to removal from the BigCommerce store, or the disconnection of a channel
		 *
		 * @param int   $count           The number of deletions added to the queue
		 * @param int[] $posts_to_delete The IDs of the posts that will be deleted
		 */
		do_action( 'bigcommerce/import/marked_deleted', $count, $posts_to_delete );

		$status->set_status( Status::MARKED_DELETED_PRODUCTS );
	}
}