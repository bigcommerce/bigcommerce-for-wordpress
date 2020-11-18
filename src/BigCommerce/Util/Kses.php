<?php


namespace BigCommerce\Util;


class Kses {

	/**
	 * Product Description Allowed HTML
	 *
	 * @param array  $allowed_tags Array of allowed tags
	 * @param string $context      Context of kses tags.
	 *
	 * @return array
	 */
	public function product_description_allowed_html( $allowed_tags, $context ) {
		if ( 'bigcommerce/product_description' === $context ) {
			return apply_filters( 'bigcommerce/product_description/allowed_html',
				array_merge(
					wp_kses_allowed_html( 'post' ),
					[
						'iframe' => [
							'src'             => true,
							'height'          => true,
							'width'           => true,
							'frameborder'     => true,
							'allowfullscreen' => true,
						],
					]
				)
			);
		}

		return $allowed_tags;
	}
}