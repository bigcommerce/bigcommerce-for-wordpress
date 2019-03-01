<?php


namespace BigCommerce\Templates;


use BigCommerce\Post_Types\Product\Product;

/**
 * Class Product_Modifiers
 *
 * @deprecated 1.7.0
 */
class Product_Modifiers extends Controller {
	const PRODUCT   = 'product';
	const MODIFIERS = 'modifiers';

	protected $template = 'components/products/product-modifiers.php';
	protected $wrapper_tag = 'div';
	protected $wrapper_classes = [ 'bc-product-form__modifiers' ];
	protected $wrapper_attributes = [ 'data-js' => 'product-modifiers' ];

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
			self::MODIFIERS => [],
		];
	}

	/**
	 * @param Product $product
	 *
	 * @return string[] The rendered modifier fields
	 */
	protected function get_modifiers( Product $product ) {
		return [];
	}
}