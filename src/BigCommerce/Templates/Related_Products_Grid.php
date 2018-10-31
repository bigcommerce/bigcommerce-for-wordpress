<?php


namespace BigCommerce\Templates;


use BigCommerce\Customizer\Sections\Product_Archive;

class Related_Products_Grid extends Controller {

	const PRODUCTS = 'products';
	const COLUMNS  = 'columns';

	protected $template = 'components/products/related-products-grid.php';

	protected function parse_options( array $options ) {
		$defaults = [
			self::PRODUCTS => [], // An array of rendered related products
			self::COLUMNS  => absint( get_option( Product_Archive::GRID_COLUMNS, 4 ) ),
		];

		return wp_parse_args( $options, $defaults );
	}

	public function get_data() {
		return [
			self::PRODUCTS => $this->options[ self::PRODUCTS ],
			self::COLUMNS  => $this->options[ self::COLUMNS ],
		];
	}
}