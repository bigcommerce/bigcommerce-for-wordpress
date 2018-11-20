<?php


namespace BigCommerce\Templates;


use BigCommerce\Post_Types\Product\Product;

class Product_Modifiers extends Controller {
	const PRODUCT   = 'product';
	const MODIFIERS = 'modifiers';

	protected $template = 'components/products/product-modifiers.php';
	protected $wrapper_tag = 'div';
	protected $wrapper_classes = [ 'bc-product-form__modifiers' ];
	protected $wrapper_attributes = [ 'data-js' => 'product-modifiers' ];

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
			self::MODIFIERS => $this->get_modifiers( $product ),
		];
	}

	/**
	 * @param Product $product
	 *
	 * @return string[] The rendered modifier fields
	 */
	protected function get_modifiers( Product $product ) {
		$data      = $product->modifiers();
		$modifiers = array_map( function ( $modifier ) {
			switch ( $modifier[ 'type' ] ) {
				case 'text':
					$class = Modifier_Types\Modifier_Text::class;
					break;
				case 'multi_line_text':
					$class = Modifier_Types\Modifier_Textarea::class;
					break;
				case 'numbers_only_text':
					$class = Modifier_Types\Modifier_Number::class;
					break;
				case 'date':
					$class = Modifier_Types\Modifier_Date::class;
					break;
				case 'checkbox':
					$class = Modifier_Types\Modifier_Checkbox::class;
					break;
				default:
					return '';
			}
			if ( $class ) {
				/** @var Modifier_Types\Modifier_Type $component */
				$component = new $class( [
					Modifier_Types\Modifier_Type::ID       => $modifier[ 'id' ],
					Modifier_Types\Modifier_Type::LABEL    => $modifier[ 'display_name' ],
					Modifier_Types\Modifier_Type::OPTIONS  => $modifier[ 'option_values' ],
					Modifier_Types\Modifier_Type::REQUIRED => (bool) $modifier[ 'required' ],
					Modifier_Types\Modifier_Type::CONFIG   => $modifier[ 'config' ],
				] );

				return $component->render();
			} else {
				return '';
			}
		}, $data );

		return array_filter( $modifiers );
	}
}