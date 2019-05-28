<?php


namespace BigCommerce\Templates;

use BigCommerce\Post_Types\Product\Product;

class Quick_View_Image extends Controller {
	const PRODUCT    = 'product';
	const IMAGE      = 'image';
	const QUICK_VIEW = 'quick_view';
	const ATTRIBUTES = 'attributes';

	protected $template = 'components/products/quick-view-image.php';

	protected function parse_options( array $options ) {
		$defaults = [
			self::PRODUCT    => null,
			self::IMAGE      => '', // the rendered image
			self::ATTRIBUTES => [], // attributes for the quick view button
		];

		return wp_parse_args( $options, $defaults );
	}

	public function get_data() {
		/** @var Product $product */
		$product = $this->options[ self::PRODUCT ];

		return [
			self::PRODUCT    => $product,
			self::IMAGE      => $this->options[ self::IMAGE ],
			self::QUICK_VIEW => $this->get_popup_template( $product ),
			self::ATTRIBUTES => $this->build_attribute_string( $this->options[ self::ATTRIBUTES ] ),
		];
	}

	protected function get_popup_template( Product $product ) {
		$component = Product_Quick_View::factory( [
			Product_Quick_View::PRODUCT => $product,
		] );

		return $component->render();
	}
}