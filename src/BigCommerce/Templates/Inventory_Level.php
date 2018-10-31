<?php


namespace BigCommerce\Templates;


class Inventory_Level extends Controller {
	const PRODUCT = 'product';

	protected $template = 'components/products/inventory-level.php';

	protected function parse_options( array $options ) {
		$defaults = [
			self::PRODUCT => null,
		];

		return wp_parse_args( $options, $defaults );
	}

	public function get_data() {
		return [
			self::PRODUCT => $this->options[ self::PRODUCT ],
		];
	}


}