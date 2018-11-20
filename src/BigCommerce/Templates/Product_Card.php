<?php


namespace BigCommerce\Templates;


use BigCommerce\Customizer\Sections\Buttons;
use BigCommerce\Post_Types\Product\Product;

class Product_Card extends Controller {
	const PRODUCT    = 'product';
	const TITLE      = 'title';
	const PRICE      = 'price';
	const BRAND      = 'brand';
	const IMAGE      = 'image';
	const FORM       = 'form';
	const QUICK_VIEW = 'quick_view';
	const ATTRIBUTES = 'attributes';

	protected $template = 'components/products/product-card.php';
	protected $wrapper_tag = 'div';
	protected $wrapper_classes = [ 'bc-product-card' ];
	protected $wrapper_attributes = [ 'data-js' => 'bc-product-loop-card' ];

	protected function parse_options( array $options ) {
		$defaults = [
			self::PRODUCT    => null,
			self::ATTRIBUTES => [],
		];

		return wp_parse_args( $options, $defaults );
	}

	public function get_data() {
		/** @var Product $product */
		$product = $this->options[ self::PRODUCT ];

		return [
			self::PRODUCT    => $product,
			self::TITLE      => $this->get_title( $product ),
			self::PRICE      => $this->get_price( $product ),
			self::BRAND      => $this->get_brand( $product ),
			self::IMAGE      => $this->get_featured_image( $product ),
			self::FORM       => $this->get_form( $product ),
			self::QUICK_VIEW => $this->get_popup_template( $product ),
			self::ATTRIBUTES => $this->build_attribute_string( $this->options[ self::ATTRIBUTES ] ),
		];
	}

	protected function get_title( Product $product ) {
		$component = Product_Title::factory( [
			Product_Title::PRODUCT => $product,
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

	protected function get_featured_image( Product $product ) {
		$component = Product_Featured_Image::factory( [
			Product_Featured_Image::PRODUCT => $product,
		] );

		return $component->render();
	}

	protected function get_form( Product $product ) {
		if ( $product->out_of_stock() ) {
			return '';
		}
		if ( $product->has_options() ) {
			$component = View_Product_Button::factory( [
				View_Product_Button::PRODUCT => $product,
				View_Product_Button::LABEL   => get_option( Buttons::CHOOSE_OPTIONS, __( 'Choose Options', 'bigcommerce' ) ),
			] );
		} else {
			$component = Product_Form::factory( [
				Product_Form::PRODUCT      => $product,
				Product_Form::SHOW_OPTIONS => false,
			] );
		}

		return $component->render();
	}

	protected function get_popup_template( Product $product ) {
		$component = Product_Quick_View::factory( [
			Product_Quick_View::PRODUCT => $product,
		] );

		return $component->render();
	}
}