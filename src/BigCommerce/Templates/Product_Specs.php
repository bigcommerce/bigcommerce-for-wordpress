<?php


namespace BigCommerce\Templates;


use BigCommerce\Post_Types\Product\Product;
use BigCommerce\Settings\Sections\Units;

class Product_Specs extends Controller {
	const PRODUCT      = 'product';
	const SPECS        = 'specs';
	const ALLOWED_HTML = 'allowed_html';

	protected $template = 'components/products/product-specs.php';

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
			self::PRODUCT      => $product,
			self::SPECS        => $this->get_specs( $product ),
			self::ALLOWED_HTML => $this->get_allowed_html(),
		];
	}

	protected function get_specs( Product $product ) {
		$specs       = [];
		/**
		 * Filters units mass.
		 *
		 * @param string $mass_unit   Mass unit.
		 */
		$mass_unit   = apply_filters( 'bigcommerce/units/mass', get_option( Units::MASS, 'oz' ) );
		/**
		 * Filters units mass.
		 *
		 * @param string $length_unit Length unit.
		 */
		$length_unit = apply_filters( 'bigcommerce/units/length', get_option( Units::LENGTH, 'in' ) );

		$weight = $product->weight;
		$width  = $product->width;
		$depth  = $product->depth;
		$height = $product->height;
		if ( $weight ) {
			$specs[ __( 'Weight:', 'bigcommerce' ) ] = sprintf( _x( '%s %s', 'weight and unit', 'bigcommerce' ), $weight, $mass_unit );
		}
		if ( $width ) {
			$specs[ __( 'Width:', 'bigcommerce' ) ] = sprintf( _x( '%s %s', 'width and unit', 'bigcommerce' ), $width, $length_unit );
		}
		if ( $depth ) {
			$specs[ __( 'Depth:', 'bigcommerce' ) ] = sprintf( _x( '%s %s', 'depth and unit', 'bigcommerce' ), $depth, $length_unit );
		}
		if ( $height ) {
			$specs[ __( 'Height:', 'bigcommerce' ) ] = sprintf( _x( '%s %s', 'height and unit', 'bigcommerce' ), $height, $length_unit );
		}

		foreach ( $product->get_custom_fields() as $field ) {
			$label = sprintf( _x( '%s:', 'product specification field label', 'bigcommerce' ), $field[ 'name' ] );

			$specs[ $label ] = $field[ 'value' ];
		}

		/**
		 * Filters product specs.
		 *
		 * @param array   $specs   Specs.
		 * @param Product $product Product.
		 */
		return apply_filters( 'bigcommerce/product/specs', $specs, $product );
	}

	protected function get_allowed_html() {
		return [
			'img' => [
				'src'    => [],
				'alt'    => [],
				'width'  => [],
				'height' => [],
			],
		];
	}

}
