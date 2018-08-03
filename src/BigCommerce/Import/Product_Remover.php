<?php


namespace BigCommerce\Import;


class Product_Remover {
	/**
	 * @var int
	 */
	private $product_id;

	/**
	 * @param int $product_id
	 */
	public function __construct( $product_id ) {
		$this->product_id = $product_id;
	}

	public function remove() {
		$post_id = $this->get_post_id();
		if ( ! empty( $post_id ) ) {
			$this->remove_images( $post_id );
			$this->remove_post( $post_id );
		}
	}

	private function get_post_id() {
		/** @var \wpdb $wpdb */
		global $wpdb;
		$post_id = $wpdb->get_var( $wpdb->prepare( "SELECT post_id FROM {$wpdb->bc_products} WHERE bc_id=%d", $this->product_id ) );

		return (int) $post_id;
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
		foreach( $image_ids as $image ) {
			wp_delete_attachment( $image, true );
		}
	}

	private function remove_post( $post_id ) {
		wp_delete_post( $post_id, true );
	}
}