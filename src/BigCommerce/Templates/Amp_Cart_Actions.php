<?php
/**
 * Overrides the Cart_Actions template controller when AMP is active.
 *
 * @package BigCommerce
 */

namespace BigCommerce\Templates;

use BigCommerce\Amp\Amp_Cart;

/**
 * Amp_Cart_Actions class
 */
class Amp_Cart_Actions extends Cart_Actions {
	const CHECKOUT_URL = 'checkout_url';

	protected function parse_options( array $options ) {
		$defaults = [
			self::CHECKOUT_URL => home_url(),
		];

		return wp_parse_args( $options, $defaults );
	}

	public function get_data() {
		return [
			self::CHECKOUT_URL => home_url( sprintf( '/bigcommerce/%s', Amp_Cart::CHECKOUT_REDIRECT_ACTION ) ),
		];
	}
}
