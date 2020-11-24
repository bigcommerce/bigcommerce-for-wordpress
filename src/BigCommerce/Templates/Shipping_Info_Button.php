<?php


namespace BigCommerce\Templates;


class Shipping_Info_Button extends Controller {

	protected $template = 'components/cart/shipping-info-button.php';
	protected $wrapper_tag = 'div';
	protected $wrapper_classes = [ 'bc-shipping-calculator' ];
	protected $wrapper_attributes = [ 'data-js' => 'bc-shipping-calculator' ];

	protected function parse_options( array $options ) {
		$defaults = [];

		return wp_parse_args( $options, $defaults );
    }

    public function get_data() {
		return [];
	}

}
