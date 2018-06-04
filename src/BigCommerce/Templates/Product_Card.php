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

	protected $template = 'components/product-card.php';

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
			self::PRODUCT    => $product,
			self::TITLE      => $this->get_title( $product ),
			self::PRICE      => $this->get_price( $product ),
			self::BRAND      => $this->get_brand( $product ),
			self::IMAGE      => $this->get_featured_image( $product ),
			self::FORM       => $this->get_form( $product ),
			self::QUICK_VIEW => $this->get_popup_template( $product ),
		];
	}

	protected function get_title( Product $product ) {
		$component = new Product_Title( [
			Product_Title::PRODUCT => $product,
		] );

		return $component->render();
	}

	protected function get_price( Product $product ) {
		$component = new Product_Price( [
			Product_Price::PRODUCT => $product,
		] );

		return $component->render();
	}

	protected function get_brand( Product $product ) {
		$component = new Product_Brand( [
			Product_Brand::PRODUCT => $product,
		] );

		return $component->render();
	}

	protected function get_featured_image( Product $product ) {
		$component = new Product_Featured_Image( [
			Product_Featured_Image::PRODUCT => $product,
		] );

		return $component->render();
	}

	protected function get_form( Product $product ) {
		if ( $product->out_of_stock() ) {
			return '';
		}
		if ( $product->has_options() ) {
			$component = new View_Product_Button( [
				View_Product_Button::PRODUCT => $product,
				View_Product_Button::LABEL => get_option( Buttons::CHOOSE_OPTIONS, __( 'Choose Options', 'bigcommerce' ) ),
			] );
		} else {
			$component = new Product_Form( [
				Product_Form::PRODUCT      => $product,
				Product_Form::SHOW_OPTIONS => false,
			] );
		}

		return $component->render();
	}

	protected function get_popup_template( Product $product ) {
		$component = new Product_Quick_View( [
			Product_Quick_View::PRODUCT => $product,
		] );

		return $component->render();
	}
}