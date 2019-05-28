<?php

namespace BigCommerce\Shortcodes;


use BigCommerce\Post_Types\Product\Product;
use BigCommerce\Templates\Product_Description;
use BigCommerce\Templates\Product_Featured_Image;
use BigCommerce\Templates\Product_Sku;
use BigCommerce\Templates\Product_Title;

class Product_Components implements Shortcode {

	const NAME = 'bc-component';

	/**
	 * Set default attributes
	 *
	 * @return array
	 */
	public static function default_attributes() {
		return [
			'id'   => '', // BigCommerce product ID
			'type' => '', // Type of product component
		];
	}

	public function render( $attributes, $instance ) {
		$attr = shortcode_atts( self::default_attributes(), $attributes, self::NAME );

		if ( ! empty( $attr['id'] ) ) {
			// The $attr['id'] is the BC product ID
			try {
				$product = Product::by_product_id( absint( $attr['id'] ) );
			} catch ( \Exception $e ) {
				return '';
			}
		} else {
			$post_id = empty( $attr['post_id'] ) ? get_the_ID() : absint( $attr['post_id'] );
			if ( empty( $post_id ) || get_post_type( $post_id ) !== Product::NAME ) {
				return '';
			}
			$product = new Product( $post_id );
		}

		switch ( $attr['type'] ) {
			case 'sku':
				$sku = Product_Sku::factory( [
					Product_Description::PRODUCT => $product,
				] );

				return $sku->render();
			case 'description':
				$description = Product_Description::factory( [
					Product_Description::PRODUCT => $product,
				] );

				return $description->render();
			case 'image':
				$product_image = Product_Featured_Image::factory( [
					Product_Featured_Image::PRODUCT => $product,
				] );

				return $product_image->render();
			default:
				$title = Product_Title::factory( [
					Product_Title::PRODUCT => $product,
				] );

				return $title->render();
		}

	}

}