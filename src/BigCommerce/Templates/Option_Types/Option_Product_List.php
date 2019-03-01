<?php


namespace BigCommerce\Templates\Option_Types;

use BigCommerce\Post_Types\Product\Product;

class Option_Product_List extends Option_Radios {

	// Uses the same template as Option_Radios

	protected function get_options() {
		$options = array_map( function ( $option ) {
			$option[ 'post_id' ]       = 0;
			$option[ 'attachment_id' ] = 0;

			if ( ! empty( $option[ 'value_data' ][ 'product_id' ] ) ) {
				$option[ 'product_id' ] = (int) $option[ 'value_data' ][ 'product_id' ];
			} else {
				$option[ 'product_id' ] = 0;
			}

			if ( ! empty( $option[ 'product_id' ] ) ) {
				$option[ 'post_id' ] = $this->get_matching_post_id( $option[ 'product_id' ] );
			}

			return $option;
		}, parent::get_options() );

		// filter out options for invalid products
		$options = array_values( array_filter( $options, function ( $option ) {
			// if the product hasn't been imported, it's probably not eligible for orders in this channel
			if ( empty( $option[ 'post_id' ] ) ) {
				return false;
			}

			$product = new Product( $option[ 'post_id' ] );

			// if the product is out of stock, it will cause a mysterious API error
			if ( $product->out_of_stock() ) {
				return false;
			}

			return true;
		} ) );

		return $options;
	}

	/**
	 * @param int $bc_id The BigCommerce product ID
	 *
	 * @return int The WordPress post ID for the product
	 */
	protected function get_matching_post_id( $bc_id ) {
		/** @var \wpdb $wpdb */
		global $wpdb;
		$sql = "SELECT post_id FROM {$wpdb->bc_products} WHERE bc_id=%d";

		return (int) $wpdb->get_var( $wpdb->prepare( $sql, $bc_id ) );
	}

}