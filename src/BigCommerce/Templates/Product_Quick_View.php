<?php


namespace BigCommerce\Templates;


use BigCommerce\Customizer\Sections\Buttons;
use BigCommerce\Post_Types\Product\Product;

class Product_Quick_View extends Product_Shortcode_Single {
	const PRODUCT = 'product';

	const PERMALINK = 'permalink';

	protected $template = 'components/product-quick-view.php';

	protected function parse_options( array $options ) {
		$defaults = [
			self::PRODUCT => null,
		];

		return wp_parse_args( $options, $defaults );
	}

	public function get_data() {
		/** @var Product $product */
		$product = $this->options[ self::PRODUCT ];

		$data                    = parent::get_data();
		$data[ self::PERMALINK ] = $this->get_permalink_button( $product );

		return $data;
	}

	protected function get_form( Product $product ) {
		if ( $product->out_of_stock() ) {
			return '';
		}

		$component = new Product_Form( [
			Product_Form::PRODUCT      => $product,
			Product_Form::SHOW_OPTIONS => true,
		] );

		return $component->render();
	}

	protected function get_permalink_button( Product $product ) {
		$component = new View_Product_Button( [
			View_Product_Button::PRODUCT => $product,
			View_Product_Button::LABEL   => $product->has_options()
				? get_option( Buttons::CHOOSE_OPTIONS, __( 'Choose Options', 'bigcommerce' ) )
				: get_option( Buttons::VIEW_PRODUCT, __( 'View Product', 'bigcommerce' ) ),
		] );

		return $component->render();
	}

	protected function get_rating( Product $product ) {
		$component = new Product_Rating( [
			Product_Rating::PRODUCT => $product,
			Product_Rating::LINK    => get_the_permalink( $product->post_id() ) . '#bc-single-product__reviews',
		] );

		return $component->render();
	}
}