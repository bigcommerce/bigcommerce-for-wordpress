<?php

namespace BigCommerce\Editor\Gutenberg\Blocks;

use BigCommerce\Shortcodes;

/**
 * Class Product_Reviews
 *
 * A block to display a given product's reviews
 */
class Product_Reviews extends Shortcode_Block {
	const NAME = 'bigcommerce/product-reviews';

	protected $icon = 'star-filled';
	protected $shortcode = Shortcodes\Product_Reviews::NAME;

	protected function title() {
		return __( 'BigCommerce Product Reviews', 'bigcommerce' );
	}

	protected function html_title() {
		return __( 'Product Reviews', 'bigcommerce' );
	}

	protected function html_image() {
		return $this->image_url( 'Gutenberg-Block_Product_Reviews.png' );
	}

	protected function keywords() {
		$keywords = parent::keywords();
		$keywords[] = __( 'reviews', 'bigcommerce' );
		return $keywords;
	}

	/**
	 * @return array
	 */
	public function js_config() {
		$config = parent::js_config();
		$config[ 'inspector' ] = [
			'header' => __( 'Review Settings', 'bigcommerce' ),
			'product_id_label' => __( 'Product ID', 'bigcommerce' ),
			'product_id_description' => __( 'The product ID from BigCommerce', 'bigcommerce' ),
		];
		return $config;
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
