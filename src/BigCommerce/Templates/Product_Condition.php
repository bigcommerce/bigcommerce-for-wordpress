<?php


namespace BigCommerce\Templates;


use BigCommerce\Post_Types\Product\Product;

class Product_Condition extends Controller {
	const PRODUCT   = 'product';
	const CONDITION = 'condition';

	protected $template = 'components/products/product-condition.php';

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
			self::PRODUCT   => $product,
			self::CONDITION => $product->show_condition() ? $product->condition() : '',
		];
	}


}