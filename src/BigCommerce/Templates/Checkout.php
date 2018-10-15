<?php

namespace BigCommerce\Templates;

class Checkout extends Controller {
	const EMBEDDED_CHECKOUT_URL = 'embedded_checkout_url';
	const STYLES = 'styles';

	protected $template = 'checkout.php';

	/**
	 * @param array $options
	 * @return array
	 */
	protected function parse_options( array $options ) {
		$defaults = [
			self::EMBEDDED_CHECKOUT_URL => null,
			self::STYLES => null,
		];

		return wp_parse_args( $options, $defaults );
	}

	/**
	 * @return array
	 */
	public function get_data() {
		return [
			self::EMBEDDED_CHECKOUT_URL => $this->options[ self::EMBEDDED_CHECKOUT_URL ],
			self::STYLES => $this->options[ self::STYLES ],
		];
	}
}
