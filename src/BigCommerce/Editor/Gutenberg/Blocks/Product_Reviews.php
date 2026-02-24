<?php

namespace BigCommerce\Editor\Gutenberg\Blocks;

use BigCommerce\Shortcodes;

/**
 * A block to display a given product's reviews.
 */
class Product_Reviews extends Shortcode_Block {
	/**
	 * The name of the block.
	 *
	 * @var string
	 */
	const NAME = 'bigcommerce/product-reviews';

	/**
	 * The block's icon.
	 *
	 * @var string
	 */
	protected $icon = 'star-filled';

	/**
	 * The shortcode used for this block.
	 *
	 * @var string
	 */
	protected $shortcode = Shortcodes\Product_Reviews::NAME;

	/**
	 * Returns the block's title.
	 *
	 * @return string The block's title.
	 */
	protected function title() {
		return __( 'BigCommerce Product Reviews', 'bigcommerce' );
	}

	/**
	 * Returns the HTML title for the block.
	 *
	 * @return string The HTML title.
	 */
	protected function html_title() {
		return __( 'Product Reviews', 'bigcommerce' );
	}

	/**
	 * Returns the URL of the image associated with the block.
	 *
	 * @return string The image URL.
	 */
	protected function html_image() {
		return $this->image_url( 'Gutenberg-Block_Product_Reviews.png' );
	}

	/**
	 * Returns an array of keywords related to the block.
	 *
	 * @return array The block's keywords.
	 */
	protected function keywords() {
		$keywords = parent::keywords();
		$keywords[] = __( 'reviews', 'bigcommerce' );
		return $keywords;
	}

	/**
	 * Returns the JavaScript configuration for the block.
	 *
	 * @return array The JavaScript configuration.
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

	/**
	 * Returns the attributes for the block.
	 *
	 * @return array The block's attributes.
	 */
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
