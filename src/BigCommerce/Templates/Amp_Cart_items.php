<?php
/**
 * Overrides the Cart_Items template controller when AMP is active.
 *
 * @package BigCommerce
 */

namespace BigCommerce\Templates;

use BigCommerce\Assets\Theme\Image_Sizes;

/**
 * Amp_Cart_Items class
 */
class Amp_Cart_Items extends Cart_Items {
	const PROXY_BASE = 'proxy_base';

	protected function parse_options( array $options ) {
		$defaults = [
			self::PROXY_BASE => '',
			self::CART       => [],
			self::IMAGE_SIZE => Image_Sizes::BC_SMALL,
		];

		return wp_parse_args( $options, $defaults );
	}

	public function get_data() {
		return array_merge(
			parent::get_data(),
			[
				self::PROXY_BASE => apply_filters( 'bigcommerce/rest/proxy_base', 'bc/v3' ),
			]
		);
	}
}
