<?php


namespace BigCommerce\Templates;

use BigCommerce\Pages\Wishlist_Page;

class Wishlist_Detail_Breadcrumb extends Controller {
	const WISHLIST      = 'wishlist';
	const WISHLISTS_URL = 'wish_lists_url';

	protected $template = 'components/wishlist/detail-breadcrumb.php';

	protected function parse_options( array $options ) {
		$defaults = [
			self::WISHLIST => null,
		];

		return wp_parse_args( $options, $defaults );
	}

	public function get_data() {
		return [
			self::WISHLIST      => $this->options[ self::WISHLIST ],
			self::WISHLISTS_URL => get_permalink( get_option( Wishlist_Page::NAME, 0 ) ),
		];
	}
}
