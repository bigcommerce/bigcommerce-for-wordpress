<?php


namespace BigCommerce\Templates;

use BigCommerce\Assets\Theme\Image_Sizes;

class Mini_Cart_Items extends Cart_Items {

	protected $template = 'components/cart/mini-cart-items.php';

	protected function parse_options( array $options ) {
		$defaults = [
			self::CART       => [],
			self::IMAGE_SIZE => Image_Sizes::BC_THUMB,
		];

		return wp_parse_args( $options, $defaults );
	}
}
