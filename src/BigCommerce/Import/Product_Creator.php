<?php

namespace BigCommerce\Import;

class Product_Creator extends Product_Saver {

	protected function save_wp_post( Product_Builder $builder ) {
		$postarr       = $this->get_post_array( $builder );
		$this->post_id = wp_insert_post( $postarr );
	}

	protected function save_product_record( Product_Builder $builder ) {
		/** @var \wpdb $wpdb */
		global $wpdb;

		// avoid errors from stray data that may have been left by careless DB admins (e.g., manually deleted posts)
		$wpdb->delete( $wpdb->bc_products, [ 'bc_id' => $this->data[ 'id' ] ], [ '%d' ] );
		$wpdb->delete( $wpdb->bc_variants, [ 'bc_id' => $this->data[ 'id' ] ], [ '%d' ] );

		$product_array = $builder->build_product_array();

		$product_array[ 'post_id' ] = $this->post_id;
		$wpdb->insert( $wpdb->bc_products, $product_array );
	}

	protected function save_product_variants( Product_Builder $builder ) {
		/** @var \wpdb $wpdb */
		global $wpdb;

		$variants = $builder->build_variants();
		foreach ( $variants as $variant ) {
			$wpdb->insert( $wpdb->bc_variants, $variant );
		}
	}
	protected function send_notifications() {
		do_action( 'bigcommerce/import/product/created', $this->post_id, $this->data, $this->api );
		parent::send_notifications();
	}
}