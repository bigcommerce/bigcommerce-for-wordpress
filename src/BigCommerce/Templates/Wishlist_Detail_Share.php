<?php


namespace BigCommerce\Templates;

use BigCommerce\Accounts\Wishlists\Wishlist;

class Wishlist_Detail_Share extends Controller {
	const WISHLIST   = 'wishlist';
	const PUBLIC_URL = 'public_url';

	protected $wrapper_tag        = 'div';
	protected $wrapper_classes    = [ 'bc-manage-wish-list-share' ];
	protected $wrapper_attributes = [ 'data-js' => 'bc-manage-wish-list-share' ];

	protected $template = 'components/wishlist/detail-share.php';

	protected function parse_options( array $options ) {
		$defaults = [
			self::WISHLIST => null,
		];

		return wp_parse_args( $options, $defaults );
	}

	public function get_data() {
		/** @var Wishlist $wishlist */
		$wishlist = $this->options[ self::WISHLIST ];

		return [
			self::WISHLIST   => $wishlist,
			self::PUBLIC_URL => $wishlist->public_url(),
		];
	}

}
