<?php

namespace BigCommerce\Templates;

class Review_List extends Controller {
	const PRODUCT        = 'product';
	const REVIEWS        = 'reviews';
	const NEXT_PAGE_URL  = 'next_page_url';
	const FIRST_PAGE_URL = 'first_page_url';
	const PAGINATION     = 'pagination';
	const WRAP           = 'wrap';

	protected $template = 'components/reviews/review-list.php';


	protected function parse_options( array $options ) {
		$defaults = [
			self::PRODUCT        => null,
			self::REVIEWS        => [],
			self::NEXT_PAGE_URL  => '',
			self::FIRST_PAGE_URL => '',
			self::WRAP           => true,
		];

		return wp_parse_args( $options, $defaults );
	}

	public function get_data() {
		$data = [
			self::REVIEWS        => $this->options[ self::REVIEWS ],
			self::FIRST_PAGE_URL => $this->options[ self::FIRST_PAGE_URL ],
			self::PAGINATION     => $this->get_pagination( $this->options[ self::FIRST_PAGE_URL ], $this->options[ self::NEXT_PAGE_URL ] ),
			self::WRAP           => $this->options[ self::WRAP ],
		];

		return $data;
	}


	protected function get_pagination( $first_page_url, $next_page_url ) {
		if ( empty( $next_page_url ) ) {
			return '';
		}
		$component = Review_List_Pagination::factory( [
			Review_List_Pagination::FIRST_PAGE_URL => $first_page_url,
			Review_List_Pagination::NEXT_PAGE_URL  => $next_page_url,
		] );

		return $component->render();
	}

}
