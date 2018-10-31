<?php


namespace BigCommerce\Templates;

class Order_History extends Controller {
	const ORDERS        = 'orders';
	const NEXT_PAGE_URL = 'next_page_url';
	const PAGINATION    = 'pagination';
	const WRAP          = 'wrap';

	protected $template = 'components/orders/order-history.php';

	protected function parse_options( array $options ) {
		$defaults = [
			self::ORDERS        => [],
			self::NEXT_PAGE_URL => '',
			self::WRAP          => true,
		];

		return wp_parse_args( $options, $defaults );
	}

	public function get_data() {
		return [
			self::ORDERS     => $this->options[ self::ORDERS ],
			self::PAGINATION => $this->get_pagination( $this->options[ self::NEXT_PAGE_URL ] ),
			self::WRAP       => $this->options[ self::WRAP ],
		];
	}


	protected function get_pagination( $next_page_url ) {
		if ( empty( $next_page_url ) ) {
			return '';
		}
		$component = Orders_Shortcode_Pagination::factory( [
			Orders_Shortcode_Pagination::NEXT_PAGE_URL => $next_page_url,
		] );

		return $component->render();
	}

}