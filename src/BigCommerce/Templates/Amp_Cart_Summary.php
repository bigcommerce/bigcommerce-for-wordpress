<?php
/**
 * Overrides the Cart_Summary template controller when AMP is active.
 *
 * @package BigCommerce
 */


namespace BigCommerce\Templates;

/**
 * Amp_Cart_Summary class
 */
class Amp_Cart_Summary extends Cart_Summary {
	const PROXY_BASE = 'proxy_base';

	protected function parse_options( array $options ) {
		$defaults = [
			self::PROXY_BASE => '',
			self::CART       => [],
		];

		return wp_parse_args( $options, $defaults );
	}

	public function get_data() {
		return [
			self::CART       => $this->options[ self::CART ],
			self::PROXY_BASE => apply_filters( 'bigcommerce/rest/proxy_base', 'bc/v3' ),
		];
	}

}
