<?php


namespace BigCommerce\Templates;

use BigCommerce\Accounts\Wishlists\Actions\Delete_Wishlist;
use BigCommerce\Accounts\Wishlists\Wishlist;

class Wishlist_Delete extends Controller {
	const WISHLIST    = 'wishlist';
	const NONCE_FIELD = 'nonce_field';

	protected $wrapper_tag        = 'script';
	protected $wrapper_classes    = [ 'bc-manage-wish-list-wrapper' ];
	protected $wrapper_attributes = [ 'type' => 'text/template' ];

	protected $template = 'components/wishlist/delete-form.php';

	protected function get_wrapper_attributes() {
		$attributes            = parent::get_wrapper_attributes();
		$attributes['data-js'] = 'bc-delete-wish-list-form--' . $this->options[ self::WISHLIST ]->list_id();

		return $attributes;
	}


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
			self::WISHLIST    => $wishlist,
			self::NONCE_FIELD => wp_nonce_field( Delete_Wishlist::ACTION . $wishlist->list_id(), '_wpnonce', true, false ),
		];
	}

}
