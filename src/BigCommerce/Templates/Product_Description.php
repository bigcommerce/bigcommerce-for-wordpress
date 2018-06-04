<?php


namespace BigCommerce\Templates;


use BigCommerce\Post_Types\Product\Product;

class Product_Description extends Controller {
	const PRODUCT = 'product';
	const CONTENT = 'content';

	protected $template = 'components/product-description.php';

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
			self::CONTENT => get_post_field( 'post_content', $product->post_id() ),
		];
	}


}