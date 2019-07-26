<?php


namespace BigCommerce\Templates;

use BigCommerce\Accounts\Wishlists\Actions\Create_Wishlist;
use BigCommerce\Accounts\Wishlists\Wishlist;

class Wishlist_Create extends Controller {
	const ACTION_URL  = 'action_url';
	const NONCE_FIELD = 'nonce_field';
	const PRODUCTS    = 'products';

	protected $wrapper_tag        = 'script';
	protected $wrapper_classes    = [ 'bc-manage-wish-list-wrapper' ];
	protected $wrapper_attributes = [ 'type' => 'text/template' ];

	protected $template = 'components/wishlist/create-form.php';

	protected function get_wrapper_attributes() {
		$attributes            = parent::get_wrapper_attributes();
		$attributes['data-js'] = 'bc-create-wish-list-form--new';

		return $attributes;
	}


	protected function parse_options( array $options ) {
		$defaults = [
			self::PRODUCTS => [],
		];

		return wp_parse_args( $options, $defaults );
	}

	public function get_data() {
		return [
			self::ACTION_URL  => Wishlist::create_url(),
			self::NONCE_FIELD => wp_nonce_field( Create_Wishlist::ACTION, '_wpnonce', true, false ),
			self::PRODUCTS    => array_map( 'intval', $this->options[ self::PRODUCTS ] ),
		];
	}

}
