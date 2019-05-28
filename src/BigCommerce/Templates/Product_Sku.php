<?php

namespace BigCommerce\Templates;

use BigCommerce\Post_Types\Product\Product;

class Product_Sku extends Controller {
	const PRODUCT = 'product';
	const SKU     = 'sku';

	protected $wrapper_tag     = 'span';
	protected $wrapper_classes = [ 'bc-product__sku' ];
	protected $template        = 'components/products/product-sku.php';

	protected function parse_options( array $options ) {
		$defaults = [
			self::PRODUCT         => null,
		];

		return wp_parse_args( $options, $defaults );
	}

	public function get_data() {
		/** @var Product $product */
		$product               = $this->options[ self::PRODUCT ];

		return [
			self::PRODUCT => $product,
			self::SKU     => $product->sku(),
		];
	}
}