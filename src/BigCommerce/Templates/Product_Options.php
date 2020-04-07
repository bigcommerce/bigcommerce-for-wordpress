<?php


namespace BigCommerce\Templates;


use BigCommerce\Assets\Theme\Image_Sizes;
use BigCommerce\Customizer\Sections\Product_Single as Customizer;
use BigCommerce\Exceptions\Component_Not_Found_Exception;
use BigCommerce\Post_Types\Product\Product;

class Product_Options extends Controller {
	const PRODUCT  = 'product';
	const OPTIONS  = 'options';
	const VARIANTS = 'variants';

	protected $template           = 'components/products/product-options.php';
	protected $wrapper_tag        = 'div';
	protected $wrapper_classes    = [ 'bc-product-form__options' ];
	protected $wrapper_attributes = [ 'data-js' => 'product-options' ];

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
		$data = array_merge( $product->options(), $product->modifiers() );

		$variant_options = $this->get_selected_variant_options( $product );

		$options = array_map( function ( $option ) use ( $variant_options ) {
			try {
				$class = $this->identify_component_class( $option['type'] );
			} catch ( Component_Not_Found_Exception $e ) {
				return '';
			}
			if ( array_key_exists( $option['id'], $variant_options ) ) {
				foreach ( $option['option_values'] as &$value ) {
					$value['is_default'] = ( $value['id'] == $variant_options[ $option['id'] ] );
				}
			}
			if ( $class ) {
				/** @var Option_Types\Option_Type $component */
				$component = new $class( [
					Option_Types\Option_Type::ID       => $option['id'],
					Option_Types\Option_Type::LABEL    => $option['display_name'],
					Option_Types\Option_Type::OPTIONS  => $option['option_values'],
					Option_Types\Option_Type::REQUIRED => (bool) $option['required'],
					Option_Types\Option_Type::CONFIG   => $option['config'],
				] );

				return $component->render();
			} else {
				return '';
			}
		}, $data );

		return array_filter( $options );
	}

	protected function identify_component_class( $type ) {
		switch ( $type ) {
			case 'dropdown':
				return Option_Types\Option_Dropdown::class;
			case 'radio_buttons':
				return Option_Types\Option_Radios::class;
			case 'rectangles':
				return Option_Types\Option_Rectangles::class;
			case 'swatch':
				return Option_Types\Option_Swatch::class;
			case 'product_list':
				return Option_Types\Option_Product_List::class;
			case 'product_list_with_images':
				return Option_Types\Option_Product_List_With_Images::class;
			case 'text':
				return Option_Types\Option_Text::class;
			case 'multi_line_text':
				return Option_Types\Option_Textarea::class;
			case 'numbers_only_text':
				return Option_Types\Option_Number::class;
			case 'date':
				return Option_Types\Option_Date::class;
			case 'checkbox':
				return Option_Types\Option_Checkbox::class;
			default:
				throw new Component_Not_Found_Exception( sprintf( __( 'No component found to handle %s option type', 'bigcommerce' ), $type ) );
		}
	}

	protected function get_selected_variant_options( Product $product ) {

		$variant_id = (int) filter_input( INPUT_GET, 'variant_id', FILTER_SANITIZE_NUMBER_INT );
		$sku        = filter_input( INPUT_GET, 'sku', FILTER_SANITIZE_STRING );
		$variants   = $this->get_variants( $product );

		if ( $sku ) {
			foreach ( $variants as $variant ) {
				if ( $variant['sku'] !== $sku ) {
					continue;
				}
				$options = [];
				foreach ( $variant['options'] as $option ) {
					$options[ $option->option_id ] = $option->id;
				}

				return $options;
			}
		} elseif ( $variant_id > 1 ) {
			foreach ( $variants as $variant ) {
				if ( $variant['variant_id'] != $variant_id ) {
					continue;
				}
				$options = [];
				foreach ( $variant['options'] as $option ) {
					$options[ $option->option_id ] = $option->id;
				}

				return $options;
			}
		}

		return [];
	}

	/**
	 * @param Product $product
	 *
	 * @return array
	 */
	private function get_variants( Product $product ) {
		$source = $product->get_source_data();
		switch ( $source->inventory_tracking ) {
			case 'none':
				$inventory = - 1;
				break;
			case 'variant':
				$inventory = null;
				break;
			case 'product':
			default:
				$inventory = $source->inventory_level;
				break;
		}

		$image_size = $this->image_size();
		$zoom_size  = $this->zoom_size();
		$variants   = array_map( function ( $variant ) use ( $inventory, $image_size, $zoom_size, $product ) {
			$data = [
				'variant_id'       => $variant->id,
				'options'          => $variant->option_values,
				'option_ids'       => wp_list_pluck( $variant->option_values, 'id' ),
				'inventory'        => isset( $inventory ) ? $inventory : $variant->inventory_level,
				'disabled'         => (bool) $variant->purchasing_disabled,
				'disabled_message' => $variant->purchasing_disabled ? $variant->purchasing_disabled_message : '',
				'sku'              => $variant->sku,
				'price'            => $variant->calculated_price,
				'formatted_price'  => $this->format_currency( $variant->calculated_price ),
				'image'            => $this->variant_image_data( $variant->id, $product->post_id(), $image_size ),
				'zoom'             => [ 'url' => '', 'width' => 0, 'height' => 0 ],
			];
			if ( $this->enable_zoom() ) {
				$data['zoom'] = $this->variant_image_data( $variant->id, $product->post_id(), $zoom_size );
			}

			return $data;
		}, $source->variants );

		return $variants;
	}


	private function image_size() {
		switch ( get_option( Customizer::GALLERY_SIZE, Customizer::SIZE_DEFAULT ) ) {
			case Customizer::SIZE_LARGE:
				$size = Image_Sizes::BC_EXTRA_MEDIUM;
				break;
			case Customizer::SIZE_DEFAULT:
			default:
				$size = Image_Sizes::BC_MEDIUM;
				break;
		}

		/**
		 * This filter is documented in Product_Gallery.php
		 */
		return apply_filters( 'bigcommerce/template/gallery/image_size', $size );
	}

	protected function zoom_size() {
		/**
		 * his filter is documented in Product_Gallery.php
		 */
		return apply_filters( 'bigcommerce/template/gallery/zoom_size', Image_Sizes::BC_LARGE );
	}

	/**
	 * Get image data for the variant.
	 *
	 * @param int    $variant_id
	 * @param int    $post_id
	 * @param string $image_size
	 *
	 * @return array|null
	 */
	private function variant_image_data( $variant_id, $post_id, $image_size ) {
		$empty = [ 'url' => '', 'width' => 0, 'height' => 0 ];

		$variant_image_map = (array) get_post_meta( $post_id, Product::VARIANT_IMAGES_META_KEY, true );

		$image_id = (int) ( ! empty( $variant_image_map[ $variant_id ] ) ? $variant_image_map[ $variant_id ] : 0 );

		if ( ! $image_id ) {
			return $empty;
		}

		$image  = wp_get_attachment_image_src( $image_id, $image_size );
		$srcset = wp_get_attachment_image_srcset( $image_id, $image_size );

		if ( ! $image ) {
			return $empty;
		}

		return [
			'url'    => $image[0],
			'width'  => $image[1],
			'height' => $image[2],
			'srcset' => $srcset,
		];
	}

	protected function enable_zoom() {
		return get_option( Customizer::ENABLE_ZOOM, 'no' ) === 'yes';
	}
}
