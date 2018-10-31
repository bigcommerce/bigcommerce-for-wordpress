<?php


namespace BigCommerce\Templates;


use BigCommerce\Post_Types\Product\Product;

class Product_Shortcode_Single extends Controller {
	const PRODUCT = 'product';

	const SKU         = 'sku';
	const TITLE       = 'title';
	const DESCRIPTION = 'description';
	const PRICE       = 'price';
	const RATING      = 'rating';
	const BRAND       = 'brand';
	const GALLERY     = 'gallery';
	const FORM        = 'form';
	const SPECS       = 'specs';

	protected $template = 'components/products/product-shortcode-single.php';

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
			self::PRODUCT     => $product,
			self::SKU         => $product->sku(),
			self::RATING      => $this->get_rating( $product ),
			self::TITLE       => $this->get_title( $product ),
			self::DESCRIPTION => $this->get_description( $product ),
			self::SPECS       => $this->get_specs( $product ),
			self::PRICE       => $this->get_price( $product ),
			self::BRAND       => $this->get_brand( $product ),
			self::GALLERY     => $this->get_gallery( $product ),
			self::FORM        => $this->get_form( $product ),
		];
	}

	protected function get_title( Product $product ) {
		$component = Product_Title::factory( [
			Product_Title::PRODUCT => $product,
		] );

		return $component->render();
	}

	protected function get_description( Product $product ) {
		$component = Product_Description::factory( [
			Product_Description::PRODUCT => $product,
		] );

		return $component->render();
	}

	protected function get_price( Product $product ) {
		$component = Product_Price::factory( [
			Product_Price::PRODUCT => $product,
		] );

		return $component->render();
	}

	protected function get_brand( Product $product ) {
		$component = Product_Brand::factory( [
			Product_Brand::PRODUCT => $product,
		] );

		return $component->render();
	}

	protected function get_gallery( Product $product ) {
		$component = Product_Gallery::factory( [
			Product_Gallery::PRODUCT => $product,
		] );

		return $component->render();
	}

	protected function get_form( Product $product ) {
		if ( $product->out_of_stock() ) {
			return '';
		}
		$component = Product_Form::factory( [
			Product_Form::PRODUCT      => $product,
			Product_Form::SHOW_OPTIONS => true,
		] );

		return $component->render();
	}

	protected function get_rating( Product $product ) {
		$component = Product_Rating::factory( [
			Product_Rating::PRODUCT => $product,
			Product_Rating::LINK    => get_the_permalink( $product->post_id() ) . '#bc-single-product__reviews',
		] );

		return $component->render();
	}

	protected function get_specs( Product $product ) {
		$component = Product_Specs::factory( [
			Product_Specs::PRODUCT => $product,
		] );

		return $component->render();
	}
}