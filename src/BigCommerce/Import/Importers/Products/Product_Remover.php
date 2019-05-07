<?php


namespace BigCommerce\Import\Importers\Products;


use BigCommerce\Post_Types\Product\Product;

class Product_Remover {

	public function remove_by_post_id( $post_id ) {
		if ( ! empty( $post_id ) ) {
			$this->remove_images( $post_id );
			$this->remove_post( $post_id );
		}
	}

	public function remove_by_product_id( $product_id, \WP_Term $channel ) {
		$post_id = $this->match_post_id( $product_id, $channel );
		$this->remove_by_post_id( $post_id );
	}

	private function match_post_id( $product_id, \WP_Term $channel ) {
		$args = [
			'meta_query'     => [
				[
					'key'   => 'bigcommerce_id',
					'value' => absint( $product_id ),
				],
			],
			'tax_query'      => [
				[
					'taxonomy' => $channel->taxonomy,
					'field'    => 'term_id',
					'terms'    => [ (int) $channel->term_id ],
					'operator' => 'IN',
				],
			],
			'post_type'      => Product::NAME,
			'post_status'    => 'any',
			'posts_per_page' => 1,
			'fields'         => 'ids',
		];

		$posts = get_posts( $args );
		if ( empty( $posts ) ) {
			return 0;
		}

		return absint( reset( $posts ) );
	}

	private function remove_images( $post_id ) {
		$image_ids = get_posts( [
			'post_type'   => 'attachment',
			'post_parent' => $post_id,
			'meta_query'  => [
				[
					'key'     => 'bigcommerce_id',
					'compare' => '>',
					'value'   => 0,
				],
			],
			'fields'      => 'ids',
		] );
		foreach ( $image_ids as $image ) {
			wp_delete_attachment( $image, true );
		}
	}

	private function remove_post( $post_id ) {
		wp_delete_post( $post_id, true );
	}
}