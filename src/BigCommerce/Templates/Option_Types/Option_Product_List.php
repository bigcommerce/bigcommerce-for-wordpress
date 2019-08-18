<?php


namespace BigCommerce\Templates\Option_Types;

use BigCommerce\Exceptions\Product_Not_Found_Exception;
use BigCommerce\Post_Types\Product\Product;

class Option_Product_List extends Option_Radios {

	// Uses the same template as Option_Radios

	protected function get_options() {
		$options = array_map( function ( $option ) {
			$option['post_id']       = 0;
			$option['attachment_id'] = 0;

			if ( ! empty( $option['value_data']['product_id'] ) ) {
				$option['product_id'] = (int) $option['value_data']['product_id'];
			} else {
				$option['product_id'] = 0;
			}

			if ( ! empty( $option['product_id'] ) ) {
				$option['post_id'] = $this->get_matching_post_id( $option['product_id'] );
			}

			return $option;
		}, parent::get_options() );

		// filter out options for invalid products
		$options = array_values( array_filter( $options, function ( $option ) {
			// if the product hasn't been imported, it's probably not eligible for orders in this channel
			if ( empty( $option['post_id'] ) ) {
				return false;
			}

			$product = new Product( $option['post_id'] );

			// if the product is out of stock, it will cause a mysterious API error
			if ( $product->out_of_stock() ) {
				return false;
			}

			return true;
		} ) );

		// add a "None" option if the field is not required
		if ( empty( $this->options[ self::REQUIRED ] ) ) {
			array_unshift( $options, [
				'id'            => 0,
				/**
				 * Filter the label given to the "None" option on a product pick list
				 *
				 * @param string $label The label of the "None" option
				 */
				'label'         => apply_filters( 'bigcommerce/template/product_list/none_option_label', __( 'None', 'bigcommerce' ) ),
				'post_id'       => 0,
				'product_id'    => 0,
				'attachment_id' => 0,
				'sort_order'    => 0,
				'value_data'    => [
					'product_id' => 0,
				],
				// set to default only if no other options are default
				'is_default'    => count( array_filter( $options, function ( $o ) {
					return $o['is_default'];
				} ) ) < 1,
			] );
		}

		return $options;
	}

	/**
	 * @param int $bc_id The BigCommerce product ID
	 *
	 * @return int The WordPress post ID for the product
	 */
	protected function get_matching_post_id( $bc_id ) {
		try {
			$product = Product::by_product_id( $bc_id );

			return (int) $product->post_id();
		} catch ( Product_Not_Found_Exception $e ) {
			return 0;
		}
	}

}
