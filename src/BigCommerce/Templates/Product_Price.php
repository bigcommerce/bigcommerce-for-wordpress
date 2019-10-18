<?php


namespace BigCommerce\Templates;


use BigCommerce\Post_Types\Product\Product;

class Product_Price extends Controller {
	const PRODUCT          = 'product';
	const SHOW_DEFAULT     = 'show_default';
	const VISIBLE          = 'visible';
	const PRICE_RANGE      = 'price_range';
	const CALCULATED_RANGE = 'calculated_price_range';
	const RETAIL_PRICE     = 'retail_price';

	protected $template           = 'components/products/product-price.php';
	protected $wrapper_tag        = 'div';
	protected $wrapper_classes    = [ 'bc-product__pricing' ];
	protected $wrapper_attributes = [ 'data-js' => 'bc-product-pricing' ];

	protected function parse_options( array $options ) {
		$defaults = [
			self::PRODUCT      => null,
			self::SHOW_DEFAULT => null,
		];

		return wp_parse_args( $options, $defaults );
	}

	public function get_data() {
		/** @var Product $product */
		$product = $this->options[ self::PRODUCT ];

		return [
			self::PRODUCT          => $product,
			self::VISIBLE          => $this->visible_class( $this->options[ self::SHOW_DEFAULT ] ),
			self::PRICE_RANGE      => $product->price_range(),
			self::CALCULATED_RANGE => $product->calculated_price_range(),
			self::RETAIL_PRICE     => $product->retail_price(),
		];
	}

	protected function get_wrapper_attributes() {
		$attributes = parent::get_wrapper_attributes();

		$attributes['data-product-price-id'] = $this->options[ self::PRODUCT ]->bc_id();

		return $attributes;
	}

	/**
	 * @param bool|null $show_default Whether to show default pricing. Null to use global setting.
	 *
	 * @return string
	 */
	protected function visible_class( $show_default ) {
		if ( $show_default === null ) {
			$show_default = ( get_option( \BigCommerce\Customizer\Sections\Product_Single::PRICE_DISPLAY, 'yes' ) !== 'no' );
		}
		if ( $show_default ) {
			return 'bc-product__pricing--visible';
		}

		return 'bc-product__pricing--hidden';
	}

}
