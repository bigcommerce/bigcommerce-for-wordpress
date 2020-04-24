<?php

namespace BigCommerce\Templates;

use BigCommerce\Assets\Theme\Image_Sizes;
use BigCommerce\Customizer\Sections\Product_Single as Customizer;
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
		$classes = array_merge( $classes, $this->get_wp_theme(), $this->get_image_size() );

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

	private function get_wp_theme() {
		$classes          = [];
		$theme            = wp_get_theme();
		$theme_name       = $theme->name;
		$parent_theme     = $theme->parent_theme;
		$supported_themes = [
			'Twenty Sixteen'                            => 'twenty-sixteen',
			'Twenty Seventeen'                          => 'twenty-seventeen',
			'Twenty Nineteen'                           => 'twenty-nineteen',
			'Twenty Twenty'                             => 'twenty-twenty',
			'Essence Pro'                               => 'genesis-essence',
			'Magazine Pro'                              => 'genesis-magazine',
			'Wellness Pro'                              => 'genesis-wellness',
			'Academy Pro'                               => 'genesis-academy',
			'Authority Pro'                             => 'genesis-authority',
			'Author Pro'                                => 'genesis-author',
			'Digital Pro'                               => 'genesis-digital',
			'Divine Theme'                              => 'divine',
			'Veganos'                                   => 'veganos',
			'Shoptimizer for BigCommerce for WordPress' => 'shoptimizer',
			'Flatsome'                                  => 'flatsome',
		];

		if ( array_key_exists( $theme_name, $supported_themes ) ) {
			$classes[] = sprintf( 'bc-wp-%s-theme', sanitize_html_class( $supported_themes[ $theme_name ] ) );
		}

		if ( $parent_theme && array_key_exists( $parent_theme, $supported_themes ) ) {
			$classes[] = sprintf( 'bc-wp-%s-theme', sanitize_html_class( $supported_themes[ $parent_theme ] ) );
		}

		return $classes;
	}

	private function get_image_size(  ) {

		switch ( get_option( Customizer::GALLERY_SIZE, Customizer::SIZE_DEFAULT ) ) {
			case Customizer::SIZE_LARGE:
				$size = Image_Sizes::BC_EXTRA_MEDIUM;
				break;
			case Customizer::SIZE_DEFAULT:
			default:
				$size = Image_Sizes::BC_MEDIUM;
				break;
		}

		return [
			sanitize_html_class( 'bc-gallery-size-' . $size ),
		];
	}
}
