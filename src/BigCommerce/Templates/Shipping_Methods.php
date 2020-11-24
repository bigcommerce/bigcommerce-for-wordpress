<?php


namespace BigCommerce\Templates;


class Shipping_Methods extends Controller {

	const METHODS = 'methods';

	protected $template = 'components/cart/shipping-methods.php';
	protected $wrapper_tag = 'div';
	protected $wrapper_classes = [ 'bc-shipping-methods' ];
	protected $wrapper_attributes = [ 'data-js' => 'bc-shipping-methods' ];

	protected function parse_options( array $options ) {
		$defaults = [
			self::METHODS => [],
		];

		return wp_parse_args( $options, $defaults );
    }
    
    public function get_data() {
		return [
			self::METHODS => $this->options[ self::METHODS ],
		];
	}

}