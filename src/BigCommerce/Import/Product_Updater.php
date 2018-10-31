<?php

namespace BigCommerce\Import;

use BigCommerce\Api\v3\Api\CatalogApi;
use BigCommerce\Api\v3\Model;

class Product_Updater extends Product_Saver {

	protected function save_wp_post( Product_Builder $builder ) {
		$postarr = $this->get_post_array( $builder );
		wp_update_post( $postarr );
	}

	protected function save_product_record( Product_Builder $builder ) {
		/** @var \wpdb $wpdb */
		global $wpdb;

		$product_array = $builder->build_product_array();
		$wpdb->update( $wpdb->bc_products, $product_array, [ 'post_id' => $this->post_id ] );
	}

	protected function save_product_variants( Product_Builder $builder ) {
		/** @var \wpdb $wpdb */
		global $wpdb;

		$variants    = $builder->build_variants();
		$variant_ids = array_column( $variants, 'variant_id' );

		$existing_variants = $wpdb->get_col( $wpdb->prepare( "SELECT variant_id FROM {$wpdb->bc_variants} WHERE bc_id=%d", $this->product[ 'id' ] ) );
		$to_remove         = array_diff( $existing_variants, $variant_ids );
		foreach ( $to_remove as $variant_id ) {
			$wpdb->delete( $wpdb->bc_variants, [ 'variant_id' => $variant_id ], [ '%d' ] );
		}

		foreach ( $variants as $variant ) {
			$variant_id = $variant[ 'variant_id' ];
			if ( in_array( $variant_id, $existing_variants ) ) {
				$wpdb->update( $wpdb->bc_variants, $variant, [ 'variant_id' => $variant_id ] );
			} else {
				$wpdb->insert( $wpdb->bc_variants, $variant );
			}
		}
	}

	protected function send_notifications() {
		/**
		 * A product has been updated by the import process
		 *
		 * @param int           $post_id The Post ID of the updated product
		 * @param Model\Product $product The product data
		 * @param Model\Listing $listing The channel listing data
		 * @param CatalogApi    $catalog The Catalog API instance
		 */
		do_action( 'bigcommerce/import/product/updated', $this->post_id, $this->product, $this->catalog );
		parent::send_notifications();
	}
}