<?php


namespace BigCommerce\Templates;


use BigCommerce\Customizer\Sections\Buttons;
use BigCommerce\Post_Types\Product\Product;
use BigCommerce\Taxonomies\Flag\Flag;

class Product_Card extends Controller {
	const PRODUCT    = 'product';
	const TITLE      = 'title';
	const PRICE      = 'price';
	const BRAND      = 'brand';
	const IMAGE      = 'image';
	const FORM       = 'form';
	const ATTRIBUTES = 'attributes';

	protected $template           = 'components/products/product-card.php';
	protected $wrapper_tag        = 'div';
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
			self::IMAGE      => $this->get_featured_image( $product, $this->options[ self::ATTRIBUTES ] ),
			self::FORM       => $this->get_form( $product ),
		];
	}

	protected function get_title( Product $product ) {
		$component = Product_Title::factory( [
			Product_Title::PRODUCT => $product,
		] );

		return $component->render();
	}

	protected function get_price( Product $product ) {
		if ( has_term( Flag::HIDE_PRICE, Flag::NAME, $product->post_id() ) ) {
			$component = Product_Hidden_Price::factory( [
				Product_Hidden_Price::PRODUCT => $product,
			] );
		} else {
			$component = Product_Price::factory( [
				Product_Price::PRODUCT => $product,
			] );
		}

		return $component->render();
	}

	protected function get_brand( Product $product ) {
		$component = Product_Brand::factory( [
			Product_Brand::PRODUCT => $product,
		] );

		return $component->render();
	}

	protected function get_featured_image( Product $product, $attributes ) {
		$quick_view = get_option( \BigCommerce\Customizer\Sections\Product_Archive::QUICK_VIEW, 'yes' );
		if ( $quick_view === 'no' ) {
			$image_component = Linked_Product_Featured_Image::factory( [
				Product_Featured_Image::PRODUCT => $product,
			] );

			return $image_component->render();
		}

		$image_component = Product_Featured_Image::factory( [
			Product_Featured_Image::PRODUCT => $product,
		] );

		$quick_view_component = Quick_View_Image::factory( [
			Quick_View_Image::PRODUCT    => $product,
			Quick_View_Image::IMAGE      => $image_component->render(),
			Quick_View_Image::ATTRIBUTES => $attributes,
		] );

		return $quick_view_component->render();
	}

	protected function get_form( Product $product ) {
		if ( ! $product->is_purchasable() ) {
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

	protected function get_wrapper_classes() {
		/** @var Product $product */
		$product = $this->options[ self::PRODUCT ];

		return [
			'bc-product-card',
			'bc-' . $product->bc_id(),
			'bc-product-availability--' . $product->availability(),
			$product->out_of_stock() ? 'bc-product-outofstock' : '',
			$product->low_inventory() ? 'bc-product-lowinventory' : '',
			$product->on_sale() ? 'bc-product-sale' : '',
		];
	}
}
