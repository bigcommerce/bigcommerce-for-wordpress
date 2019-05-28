<?php


namespace BigCommerce\Templates;


use BigCommerce\Customizer\Sections\Buttons;
use BigCommerce\Post_Types\Product\Product;

class Product_Quick_View extends Product_Shortcode_Single {
	const PRODUCT = 'product';

	const PERMALINK = 'permalink';

	protected $template = 'components/products/product-quick-view.php';

	protected $wrapper_tag        = 'div';
	protected $wrapper_classes    = [ 'bc-product-card', 'bc-product-card--single' ];
	protected $wrapper_attributes = [ 'data-js' => 'bc-product-data-wrapper' ];


	protected function get_wrapper_attributes() {
		$attributes = $this->wrapper_attributes;
		$attributes['id'] = sprintf( 'bc-product-%s--quick-view', esc_attr( $this->options[ self::PRODUCT ]->sku() ) );
		return $attributes;
	}

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
		$component = Product_Form::factory( [
			Product_Form::PRODUCT      => $product,
			Product_Form::SHOW_OPTIONS => true,
		] );

		return $component->render();
	}

	protected function get_permalink_button( Product $product ) {
		$component = View_Product_Button::factory( [
			View_Product_Button::PRODUCT => $product,
			View_Product_Button::LABEL   => $product->has_options()
				? get_option( Buttons::CHOOSE_OPTIONS, __( 'Choose Options', 'bigcommerce' ) )
				: get_option( Buttons::VIEW_PRODUCT, __( 'View Product', 'bigcommerce' ) ),
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
}