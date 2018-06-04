<?php


namespace BigCommerce\Templates;


use BigCommerce\Container\Assets;
use BigCommerce\Post_Types\Product\Product;

class Product_Gallery extends Controller {
	const PRODUCT   = 'product';
	const IMAGE_IDS = 'image_ids';
	const FALLBACK  = 'fallback_image';

	protected $template = 'components/product-gallery.php';

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
			self::IMAGE_IDS => $product->get_gallery_ids(),
			self::FALLBACK  => $this->get_fallback(),
		];
	}

	protected function get_fallback() {
		$component = new Fallback_Image( [] );

		return $component->render();
	}


}