<?php


namespace BigCommerce\Import\Importers\Products;


use BigCommerce\Post_Types\Product\Product;

/**
 * Handles the removal of products from WordPress by matching them with their
 * corresponding BigCommerce product IDs and channel terms.
 * 
 * This class provides methods for deleting products either by WordPress post ID
 * or by matching a BigCommerce product ID with its associated WordPress post.
 * It also ensures that the correct product is targeted based on the channel term
 * associated with the product.
 *
 * @package BigCommerce\Import\Importers\Products
 */
class Product_Remover {

    /**
     * Removes a product by its WordPress post ID.
     *
     * This method will delete the product post from WordPress if the post ID is not empty.
     *
     * @param int $post_id The WordPress post ID of the product to be removed.
     */
    public function remove_by_post_id( $post_id ) {
        if ( ! empty( $post_id ) ) {
            $this->remove_post( $post_id );
        }
    }

    /**
     * Removes a product by its BigCommerce product ID and channel term.
     *
     * This method will first match the WordPress post ID for the given BigCommerce product ID and channel,
     * then proceed to remove the corresponding WordPress post.
     *
     * @param int       $product_id The BigCommerce product ID.
     * @param \WP_Term  $channel    The WordPress term representing the channel to which the product belongs.
     */
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

	private function remove_post( $post_id ) {
		wp_delete_post( $post_id, true );
	}
}
