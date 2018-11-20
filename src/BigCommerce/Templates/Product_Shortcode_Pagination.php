<?php


namespace BigCommerce\Templates;


class Product_Shortcode_Pagination extends Controller {
	const NEXT_PAGE_URL = 'next_page_url';

	protected $template = 'components/products/product-shortcode-pagination.php';
	protected $wrapper_tag = 'div';
	protected $wrapper_classes = [ 'bc-load-items__trigger', 'bc-load-items__trigger--posts' ];
	protected $wrapper_attributes = [ 'data-js' => 'load-items-trigger' ];

	protected function parse_options( array $options ) {
		$defaults = [
			self::NEXT_PAGE_URL => '',
		];

		return wp_parse_args( $options, $defaults );
	}

	public function get_data() {
		return [
			self::NEXT_PAGE_URL => $this->options[ self::NEXT_PAGE_URL ],
		];
	}


}