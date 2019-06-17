<?php


namespace BigCommerce\Templates;


use BigCommerce\Post_Types\Product\Product;

class Product_Hidden_Price extends Controller {
	const PRODUCT = 'product';
	const MESSAGE = 'message';

	protected $template        = 'components/products/product-hidden-price.php';
	protected $wrapper_tag     = 'div';
	protected $wrapper_classes = [ 'bc-product__pricing' ];

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
			self::MESSAGE => $this->get_message( $product ),
		];
	}

	protected function get_message( Product $product ) {
		return $product->price_hidden_label;
	}

}