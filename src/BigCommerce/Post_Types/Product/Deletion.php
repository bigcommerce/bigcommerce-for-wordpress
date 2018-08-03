<?php


namespace BigCommerce\Post_Types\Product;


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
		if ( $bc_id ) {
			$this->remove_variants( $bc_id );
			$this->remove_product( $bc_id );
			$this->remove_reviews( $bc_id );
		}
	}

	private function remove_variants( $product_id ) {
		/** @var \wpdb $wpdb */
		global $wpdb;
		$wpdb->delete( $wpdb->bc_variants, [ 'bc_id' => $product_id ], [ '%d' ] );
	}

	private function remove_product( $product_id ) {
		/** @var \wpdb $wpdb */
		global $wpdb;
		$wpdb->delete( $wpdb->bc_products, [ 'bc_id' => $product_id ], [ '%d' ] );
	}

	private function remove_reviews( $product_id ) {
		/** @var \wpdb $wpdb */
		global $wpdb;
		$wpdb->delete( $wpdb->bc_reviews, [ 'bc_id' => $product_id ], [ '%d' ] );
	}
}