<?php


namespace BigCommerce\Post_Types\Product;


use BigCommerce\Taxonomies\Channel\Channel;

class Deletion {
	/**
	 * @param int $post_id
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
		if ( $bc_id ) {
			$another_channel_post = $this->get_post_in_another_channel( $bc_id, $post_id );
			if ( $another_channel_post ) {
				// re-assign the child images to another post
				$this->reparent_images( $post_id, $another_channel_post );
			} else {
				// the product is complete gone from the site, so get rid of all related imported content
				$this->remove_reviews( $bc_id );
				$this->remove_images( $post_id );
			}
		}
	}

	/**
	 * Remove any reviews related to the product
	 *
	 * @param int $product_id
	 *
	 * @return void
	 */
	private function remove_reviews( $product_id ) {
		/** @var \wpdb $wpdb */
		global $wpdb;
		$wpdb->delete( $wpdb->bc_reviews, [ 'bc_id' => $product_id ], [ '%d' ] );
	}

	/**
	 * Remove all images related to the product
	 *
	 * @param int $post_id
	 *
	 * @return void
	 */
	private function remove_images( $post_id ) {
		$image_ids = $this->identify_child_attachments( $post_id );
		foreach ( $image_ids as $image ) {
			wp_delete_attachment( $image, true );
		}
	}

	/**
	 * Set the attachment parent to a new post
	 *
	 * @param int $old_parent_id
	 * @param int $new_parent_id
	 *
	 * @return void
	 */
	private function reparent_images( $old_parent_id, $new_parent_id ) {
		$image_ids = $this->identify_child_attachments( $old_parent_id );
		if ( empty( $image_ids ) || empty( $new_parent_id ) ) {
			return; // nothing to do
		}
		$id_string = implode(',', array_map( 'intval', $image_ids ) );

		/** @var \wpdb $wpdb */
		global $wpdb;
		$wpdb->query( $wpdb->prepare( "UPDATE {$wpdb->posts} SET post_parent=%s WHERE post_parent=%s AND ID IN ($id_string)", $new_parent_id, $old_parent_id ) );

		foreach ( $image_ids as $image_id ) {
			clean_post_cache( $image_id );
		}
	}

	/**
	 * Find all imported attachments with the given post as a parent
	 *
	 * @param int $post_id
	 *
	 * @return int[]
	 */
	private function identify_child_attachments( $post_id ) {
		return get_posts( [
			'post_type'      => 'attachment',
			'post_parent'    => $post_id,
			'meta_query'     => [
				[
					'key'     => 'bigcommerce_id',
					'compare' => '>',
					'value'   => 0,
				],
			],
			'fields'         => 'ids',
			'posts_per_page' => - 1,
		] );
	}

	/**
	 * Determine if there are other posts with the same product ID in other channels
	 *
	 * @param int $bc_id
	 * @param int $post_id
	 *
	 * @return int A post ID for the product in another channel
	 */
	private function get_post_in_another_channel( $bc_id, $post_id ) {
		$post_channels = get_the_terms( $post_id, Channel::NAME );
		if ( ! $post_channels ) {
			return 0;
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

		if ( empty( $matches ) ) {
			return 0;
		}

		return (int) reset( $matches );
	}
}
