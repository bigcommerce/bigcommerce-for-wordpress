<?php


namespace BigCommerce\Templates;


use BigCommerce\Post_Types\Product\Product;

class Product_Form extends Controller {
	const PRODUCT = 'product';
	const OPTIONS = 'options';
	const BUTTON  = 'button';

	const SHOW_OPTIONS = 'show_options';

	protected $template = 'components/product-form.php';

	protected function parse_options( array $options ) {
		$defaults = [
			self::PRODUCT      => null,
			self::SHOW_OPTIONS => true,
		];

		return wp_parse_args( $options, $defaults );
	}

	public function get_data() {
		/** @var Product $product */
		$product = $this->options[ self::PRODUCT ];

		return [
			self::PRODUCT => $product,
			self::BUTTON  => $product->purchase_button(),
			self::OPTIONS => $this->options[ self::SHOW_OPTIONS ] ? $this->get_options( $product ) : '',
		];
	}

	protected function get_options( Product $product ) {
		$component = new Product_Options( [
			Product_Options::PRODUCT => $product,
		] );
		return $component->render();
	}
}