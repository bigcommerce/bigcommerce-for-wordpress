<?php

namespace BigCommerce\Templates;

use BigCommerce\Post_Types\Product\Product;

class Body_Classes {
	/**
	 * Set body classes on the front end template
	 *
	 * @param string[] $classes
	 *
	 * @return string[]
	 * @filter body_class
	 */
	public function set_body_classes( $classes ) {
		if ( is_singular( Product::NAME ) ) {
			$classes = array_merge( $classes, $this->product_single_classes( get_queried_object_id() ) );
		}

		return $classes;
	}

	private function product_single_classes( $post_id ) {
		$classes = [];
		$product = new Product( get_queried_object_id() );
		
		$classes[] = sprintf( 'bc-product-%d', $product->bc_id() );
		$classes[] = sprintf( 'bc-availability-%s', $product->availability() );

		if ( $product->on_sale() ) {
			$classes[] = 'bc-product-sale';
		}
		if ( $product->out_of_stock() ) {
			$classes[] = 'bc-product-outofstock';
		}
		if ( $product->low_inventory() ) {
			$classes[] = 'bc-product-lowinventory';
		}

		return $classes;
	}
}
