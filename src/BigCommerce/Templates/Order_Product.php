<?php


namespace BigCommerce\Templates;

use BigCommerce\Assets\Theme\Image_Sizes;
use BigCommerce\Customizer\Sections;
use BigCommerce\Exceptions\Product_Not_Found_Exception;
use BigCommerce\Post_Types\Product\Product;
use BigCommerce\Taxonomies\Brand\Brand;
use BigCommerce\Taxonomies\Condition\Condition;
use BigCommerce\Taxonomies\Flag\Flag;

class Order_Product extends Controller {
	const PRODUCT        = 'product';
	const CHANNEL        = 'channel';
	const THUMBNAIL_SIZE = 'thumbnail_size';

	const TITLE            = 'title';
	const IMAGE_ID         = 'image_id';
	const IMAGE            = 'image';
	const QUANTITY_ORDERED = 'quantity_ordered';
	const QUANTITY_SHIPPED = 'quantity_shipped';
	const UNIT_PRICE       = 'unit_price';
	const TOTAL_PRICE      = 'total_price';
	const SKU              = 'sku';
	const OPTIONS          = 'options';
	const PERMALINK        = 'permalink';

	protected $template = 'components/orders/order-product.php';

	protected function parse_options( array $options ) {
		$defaults = [
			self::PRODUCT        => null,
			self::CHANNEL        => null,
			/**
			 * Filter the image size for the order history page
			 *
			 * @param string $size The image size to us
			 */
			self::THUMBNAIL_SIZE => apply_filters( 'bigcommerce/template/order_history/image_size', Image_Sizes::BC_SMALL ),
		];

		return wp_parse_args( $options, $defaults );
	}

	public function get_data() {
		$product = $this->options[ self::PRODUCT ];
		$post_id = $product['product_id'] ? $this->get_product_post( $product['product_id'], $this->options[ self::CHANNEL ] ) : 0;

		$image_id = $post_id ? $this->get_image_id( $post_id, $product['variant_id'] ) : 0;
		$image    = $image_id ? wp_get_attachment_image( $image_id, $this->options[ self::THUMBNAIL_SIZE ] ) : $this->get_fallback_image( $this->options[ self::THUMBNAIL_SIZE ] );

		$data = [
			self::TITLE            => $post_id ? get_the_title( $post_id ) : $product['name'],
			self::IMAGE_ID         => $image_id,
			self::IMAGE            => $image,
			Brand::NAME            => $post_id ? $this->get_terms( $post_id, Brand::NAME ) : [],
			Condition::NAME        => ( $post_id && has_term( Flag::SHOW_CONDITION, Flag::NAME, $post_id ) ) ? $this->get_terms( $post_id, Condition::NAME ) : [],
			self::QUANTITY_ORDERED => $product['quantity'],
			self::QUANTITY_SHIPPED => $product['quantity_shipped'],
			self::UNIT_PRICE       => $this->format_currency( $product['base_price'] ),
			self::TOTAL_PRICE      => $this->format_currency( $product['base_total'] ),
			self::SKU              => $product['sku'],
			self::OPTIONS          => $this->get_options( $product ),
			self::PERMALINK        => $post_id ? get_the_permalink( $post_id ) : '',
		];

		return $data;
	}

	/**
	 * @param int      $product_id
	 * @param \WP_Term $channel
	 *
	 * @return int The ID of the WP post associated with the product ID
	 */
	private function get_product_post( $product_id, $channel = null ) {
		if ( empty( $product_id ) ) {
			return 0;
		}

		try {
			$product = Product::by_product_id( $product_id, $channel );
			return $product->post_id();
		} catch ( Product_Not_Found_Exception $e ) {
			return 0;
		}
	}

	/**
	 * Get the ID of the product's featured image
	 *
	 * @param int $post_id
	 *
	 * @return int
	 */
	protected function get_image_id( $post_id, $variant_id = 0 ) {
		if ( empty( $post_id ) ) {
			return 0;
		}

		if ( $variant_id ) {
			$variant_image_map = (array) get_post_meta( $post_id, Product::VARIANT_IMAGES_META_KEY, true );
			if ( ! empty( $variant_image_map[ $variant_id ] ) ) {
				return $variant_image_map[ $variant_id ];
			}
		}

		$thumbnail_id = (int) get_post_thumbnail_id( $post_id );
		if ( $thumbnail_id ) {
			return $thumbnail_id;
		}

		return 0;
	}

	private function get_terms( $post_id, $taxonomy ) {
		$terms = get_the_terms( $post_id, $taxonomy );

		if ( empty( $terms ) || is_wp_error( $terms ) ) {
			return [];
		}

		return wp_list_pluck( $terms, 'name' );
	}

	private function get_options( $product ) {
		if ( empty( $product['product_options'] ) ) {
			return [];
		}

		return array_map( function ( $option ) {
			return [
				'label' => $option->display_name,
				'value' => $option->display_value,
			];
		}, $product['product_options'] );
	}

	protected function get_fallback_image( $size ) {
		$default = get_option( Sections\Product_Single::DEFAULT_IMAGE, 0 );
		if ( empty( $default ) ) {
			$component = Fallback_Image::factory( [] );

			return $component->render();
		}

		return wp_get_attachment_image( $default, $size );
	}

}
