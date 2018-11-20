<?php

namespace BigCommerce\Editor\Gutenberg\Blocks;

use BigCommerce\Shortcodes;

/**
 * Class Cart
 *
 * A block to display a given product reviews block
 */
class Product_Reviews extends Gutenberg_Block {
	const NAME = 'bigcommerce/product-reviews';

	/**
	 * @param array $attributes
	 *
	 * @return string
	 */
	public function render( $attributes ) {
		if ( empty( $attributes[ 'shortcode' ] ) ) {
			return sprintf( '[%s]', Shortcodes\Product_Reviews::NAME );
		}
		return $attributes[ 'shortcode' ]; // content will be passed through do_shortcode
	}

	/**
	 * @return array
	 */
	public function js_config() {
		return [
			'name'      => $this->name(),
			'title'     => __( 'BigCommerce Product Reviews', 'bigcommerce' ),
			'category'  => 'widgets',
			'keywords'  => [
				__( 'ecommerce', 'bigcommerce' ),
				__( 'commerce', 'bigcommerce' ),
				__( 'reviews', 'bigcommerce' ),
			],
			'shortcode' => Shortcodes\Product_Reviews::NAME,
			'block_html' => [
				'title' => __( 'Product Reviews', 'bigcommerce' ),
			],
			'inspector' => [
				'header' => __( 'Review Settings', 'bigcommerce' ),
				'product_id_label' => __( 'Product ID', 'bigcommerce' ),
				'product_id_description' => __( 'The product ID from BigCommerce', 'bigcommerce' ),
			],
		];
	}

	protected function attributes() {
		return [
			'shortcode' => [
				'type' => 'string',
			],
			'productId' => [
				'type' => 'string',
			],
		];
	}
}
