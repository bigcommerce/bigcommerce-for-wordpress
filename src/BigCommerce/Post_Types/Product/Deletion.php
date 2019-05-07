<?php


namespace BigCommerce\Post_Types\Product;


use BigCommerce\Taxonomies\Channel\Channel;
use BigCommerce\Taxonomies\Channel\Connections;

class Deletion {
	/**
	 * @param $post_id
	 *
	 * @return void
	 * @action before_delete_post
	 */
	public function delete_product_data( $post_id ) {
		if ( get_post_type( $post_id ) !== Product::NAME ) {
			return;
		}
		$product = new Product( $post_id );
		$bc_id   = $product->bc_id();
		if ( $bc_id && ! $this->has_posts_in_other_channels( $bc_id, $post_id ) ) {
			$this->remove_reviews( $bc_id );
		}
	}

	private function remove_reviews( $product_id ) {
		/** @var \wpdb $wpdb */
		global $wpdb;
		$wpdb->delete( $wpdb->bc_reviews, [ 'bc_id' => $product_id ], [ '%d' ] );
	}

	/**
	 * Determine if there are other posts with the same product ID in other channels
	 *
	 * @param int $bc_id
	 * @param int $post_id
	 *
	 * @return bool
	 */
	private function has_posts_in_other_channels( $bc_id, $post_id ) {
		$connections = new Connections();
		$channels    = $connections->active();
		if ( count( $channels ) <= 1 ) {
			return false;
		}

		$post_channels = get_the_terms( $post_id, Channel::NAME );
		if ( ! $post_channels ) {
			return false;
		}
		$post_channel = reset( $post_channels );

		$matches = get_posts( [
			'posts_per_page' => 1,
			'fields'         => 'ids',
			'post_type'      => Product::NAME,
			'post_status'    => 'any',
			'meta_query'     => [
				[
					'key'   => 'bigcommerce_id',
					'value' => absint( $bc_id ),
				],
			],
			'tax_query'      => [
				[
					'taxonomy' => Channel::NAME,
					'field'    => 'term_id',
					'terms'    => [ (int) $post_channel->term_id ],
					'operator' => 'NOT IN',
				],
			],
		] );

		return ! empty( $matches );
	}
}