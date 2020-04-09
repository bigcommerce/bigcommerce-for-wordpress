<?php

namespace BigCommerce\Shortcodes;


use BigCommerce\Assets\Theme\Image_Sizes;
use BigCommerce\Customizer\Sections\Product_Single as Customizer;
use BigCommerce\Editor\Gutenberg\Blocks\Product_Components as Components;
use BigCommerce\Post_Types\Product\Product;
use BigCommerce\Templates\Product_Description;
use BigCommerce\Templates\Product_Featured_Image;
use BigCommerce\Templates\Product_Form;
use BigCommerce\Templates\Product_Form_Preview;
use BigCommerce\Templates\Product_Not_Available;
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
			'id'      => 0, // BigCommerce product ID
			'post_id' => 0, // WordPress post ID
			'type'    => '', // Type of product component
			'preview' => 0, // internal use: set to 1 to remove interactive elements
		];
	}

	public function render( $attributes, $instance ) {
		$attr = shortcode_atts( self::default_attributes(), $attributes, self::NAME );

		if ( ! empty( $attr['id'] ) ) {
			// The $attr['id'] is the BC product ID
			try {
				$product = Product::by_product_id( absint( $attr['id'] ) );
			} catch ( \Exception $e ) {
				return $this->product_not_found();
			}
		} else {
			$post_id = empty( $attr['post_id'] ) ? get_the_ID() : absint( $attr['post_id'] );
			if ( empty( $post_id ) || get_post_type( $post_id ) !== Product::NAME ) {
				return $this->product_not_found();
			}
			$product = new Product( $post_id );
		}

		switch ( $attr['type'] ) {
			case Components::SKU:
				$sku = Product_Sku::factory( [
					Product_Description::PRODUCT => $product,
				] );

				return $sku->render();
			case Components::DESCRIPTION:
				$description = Product_Description::factory( [
					Product_Description::PRODUCT => $product,
				] );

				return $description->render();
			case Components::IMAGE:
				$product_image = Product_Featured_Image::factory( [
					Product_Featured_Image::PRODUCT => $product,
					Product_Featured_Image::SIZE    => $this->image_size(),
					Product_Featured_Image::CLASSES => [ 'bc-component' ],
				] );

				return $product_image->render();
			case Components::ADD_TO_CART:
				if ( $attr['preview'] ) {
					$product_form = Product_Form_Preview::factory( [
						Product_Form::PRODUCT      => $product,
						Product_Form::SHOW_OPTIONS => false,
					] );
				} else {
					$product_form = Product_Form::factory( [
						Product_Form::PRODUCT => $product,
					] );
				}

				return $product_form->render();
			default:
				$title_args = [
					Product_Title::PRODUCT        => $product,
					Product_Title::SHOW_CONDITION => false,
					Product_Title::SHOW_INVENTORY => false,
				];
				if ( $attr['preview'] ) {
					$title_args[ Product_Title::USE_PERMALINK ] = false;
				}
				$title = Product_Title::factory( $title_args );

				return $title->render();
		}

	}

	/**
	 * If a product cannot be found, display a message
	 * in place of the requested component
	 *
	 * @return string
	 */
	private function product_not_found() {
		$component = Product_Not_Available::factory();

		return $component->render();
	}

	private function image_size() {
		switch ( get_option( Customizer::GALLERY_SIZE, Customizer::SIZE_DEFAULT ) ) {
			case Customizer::SIZE_LARGE:
				$size = Image_Sizes::BC_EXTRA_MEDIUM;
				break;
			case Customizer::SIZE_DEFAULT:
			default:
				$size = Image_Sizes::BC_MEDIUM;
				break;
		}

		/**
		 * Filter the image size used for product gallery images
		 *
		 * @param string $size The image size to use
		 */
		return apply_filters( 'bigcommerce/template/gallery/image_size', $size );
	}

}
