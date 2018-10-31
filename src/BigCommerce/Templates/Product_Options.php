<?php


namespace BigCommerce\Templates;


use BigCommerce\Post_Types\Product\Product;

class Product_Options extends Controller {
	const PRODUCT  = 'product';
	const OPTIONS  = 'options';
	const VARIANTS = 'variants';

	protected $template = 'components/products/product-options.php';

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
			self::PRODUCT  => $product,
			self::OPTIONS  => $this->get_options( $product ),
			self::VARIANTS => $this->get_variants( $product ),
		];
	}

	/**
	 * @param Product $product
	 *
	 * @return string[] The rendered option fields
	 */
	protected function get_options( Product $product ) {
		$data    = $product->options();
		$options = array_map( function ( $option ) {
			switch ( $option[ 'type' ] ) {
				case 'dropdown':
					$class = Option_Types\Option_Dropdown::class;
					break;
				case 'radio_buttons':
					$class = Option_Types\Option_Radios::class;
					break;
				case 'rectangles':
					$class = Option_Types\Option_Rectangles::class;
					break;
				case 'swatch':
					$class = Option_Types\Option_Swatch::class;
					break;
				case 'product_list':
					$class = Option_Types\Option_Radios::class;
					break;
				case 'product_list_with_images':
					$class = Option_Types\Option_Product_List_With_Images::class;
					break;
				default:
					return '';
			}
			if ( $class ) {
				/** @var Option_Types\Option_Type $component */
				$component = new $class( [
					Option_Types\Option_Type::ID      => $option[ 'id' ],
					Option_Types\Option_Type::LABEL   => $option[ 'display_name' ],
					Option_Types\Option_Type::OPTIONS => $option[ 'option_values' ],
				] );

				return $component->render();
			} else {
				return '';
			}
		}, $data );

		return array_filter( $options );
	}

	/**
	 * @param Product $product
	 *
	 * @return array
	 */
	private function get_variants( Product $product ) {
		$source   = $product->get_source_data();
		switch ( $source->inventory_tracking ) {
			case 'none':
				$inventory = -1;
				break;
			case 'variant':
				$inventory = null;
				break;
			case 'product':
			default:
				$inventory = $source->inventory_level;
				break;
		}
		$variants = array_map( function( $variant ) use ( $inventory ) {
			$data = [
				'variant_id' => $variant->id,
				'options' => $variant->option_values,
				'option_ids' => wp_list_pluck( $variant->option_values, 'id' ),
				'inventory' => isset( $inventory ) ? $inventory : $variant->inventory_level,
				'disabled' => (bool) $variant->purchasing_disabled,
				'disabled_message' => $variant->purchasing_disabled ? $variant->purchasing_disabled_message : '',
				'sku' => $variant->sku,
				'price' => $variant->calculated_price,
			];
			return $data;
		}, $source->variants );
		return $variants;
	}
}