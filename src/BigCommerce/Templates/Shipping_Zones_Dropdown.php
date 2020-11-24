<?php


namespace BigCommerce\Templates;


class Shipping_Zones_Dropdown extends Controller {

	const ZONES = 'zones';

	protected $template = 'components/cart/shipping-zones-dropdown.php';
	protected $wrapper_tag = 'div';
	protected $wrapper_classes = [];
	protected $wrapper_attributes = [ 'data-js' => 'bc-shipping-zones' ];

	protected function parse_options( array $options ) {
		$defaults = [
			self::ZONES => [],
		];

		return wp_parse_args( $options, $defaults );
    }
    
    public function get_data() {
		return [
			self::ZONES => $this->options[ self::ZONES ],
		];
	}

}