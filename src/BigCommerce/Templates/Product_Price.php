<?php


namespace BigCommerce\Templates;


use BigCommerce\Post_Types\Product\Product;

class Product_Price extends Controller {
	const PRODUCT = 'product';

	protected $template = 'components/products/product-price.php';
	protected $wrapper_tag = 'p';
	protected $wrapper_classes = [ 'bc-product__pricing' ];
	protected $wrapper_attributes = [ 'data-js' => 'bc-product-pricing' ];

	protected function parse_options( array $options ) {
		$defaults = [
			self::PRODUCT => null,
		];

		return wp_parse_args( $options, $defaults );
	}

	public function get_data() {
		/** @var Product $product */
		$product = $this->options[ self::PRODUCT ];

		return [
			self::PRODUCT => $product,
		];
	}


}