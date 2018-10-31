<?php


namespace BigCommerce\Templates;


use BigCommerce\Customizer\Sections\Product_Archive;

class Product_Shortcode_Grid extends Controller {
	const CARDS         = 'cards';
	const NEXT_PAGE_URL = 'next_page_url';
	const PAGINATION    = 'pagination';
	const WRAP          = 'wrap';
	const COLUMNS       = 'columns';

	protected $template = 'components/products/product-shortcode-grid.php';

	protected function parse_options( array $options ) {
		$defaults = [
			self::CARDS         => [],
			self::NEXT_PAGE_URL => '',
			self::WRAP          => true,
			self::COLUMNS       => absint( get_option( Product_Archive::GRID_COLUMNS, 4 ) ),
		];

		return wp_parse_args( $options, $defaults );
	}

	public function get_data() {
		return [
			self::CARDS      => $this->options[ self::CARDS ],
			self::PAGINATION => $this->get_pagination( $this->options[ self::NEXT_PAGE_URL ] ),
			self::WRAP       => $this->options[ self::WRAP ],
			self::COLUMNS    => $this->options[ self::COLUMNS ],
		];
	}

	protected function get_pagination( $next_page_url ) {
		if ( empty( $next_page_url ) ) {
			return '';
		}
		$component = Product_Shortcode_Pagination::factory( [
			Product_Shortcode_Pagination::NEXT_PAGE_URL => $next_page_url,
		] );

		return $component->render();
	}

}