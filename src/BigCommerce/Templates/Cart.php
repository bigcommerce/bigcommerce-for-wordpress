<?php


namespace BigCommerce\Templates;

use BigCommerce\Assets\Theme\Image_Sizes;
use BigCommerce\Customizer\Sections;


class Cart extends Controller {
	const CART           = 'cart';
	const FALLBACK_IMAGE = 'fallback_image';
	const IMAGE_SIZE     = 'image_size';

	protected $template = 'cart.php';

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
			self::FALLBACK_IMAGE => $this->get_fallback_image(),
			self::IMAGE_SIZE     => $this->options[ self::IMAGE_SIZE ],
		];
	}

	protected function get_fallback_image() {
		$default = get_option( Sections\Product_Single::DEFAULT_IMAGE, 0 );
		if ( empty( $default ) ) {
			$component = new Fallback_Image( [] );

			return $component->render();
		}

		return wp_get_attachment_image( $default, $this->options[ self::IMAGE_SIZE ] );
	}


}