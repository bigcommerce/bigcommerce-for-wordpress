<?php


namespace BigCommerce\Templates;

use BigCommerce\Assets\Theme\Image_Sizes;
use BigCommerce\Customizer\Sections;

class Cart_Items extends Controller {
	const CART           = 'cart';
	const IMAGE_SIZE     = 'image_size';
	const FALLBACK_IMAGE = 'fallback_image';

	protected $template = 'components/cart/cart-items.php';
	protected $wrapper_tag = 'div';
	protected $wrapper_classes = [ 'bc-cart-body' ];

	protected function parse_options( array $options ) {
		$defaults = [
			self::CART       => [],
			self::IMAGE_SIZE => Image_Sizes::BC_SMALL,
		];

		return wp_parse_args( $options, $defaults );
	}

	public function get_data() {
		return [
			self::CART           => $this->options[ self::CART ],
			self::IMAGE_SIZE     => $this->options[ self::IMAGE_SIZE ],
			self::FALLBACK_IMAGE => $this->get_fallback_image(),
		];
	}

	protected function get_fallback_image() {
		$default = get_option( Sections\Product_Single::DEFAULT_IMAGE, 0 );
		if ( empty( $default ) ) {
			$component = Fallback_Image::factory( [] );

			return $component->render();
		}

		return wp_get_attachment_image( $default, $this->options[ self::IMAGE_SIZE ] );
	}

}