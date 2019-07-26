<?php


namespace BigCommerce\Templates;

use BigCommerce\Accounts\Wishlists\Wishlist;

class Wishlist_List extends Controller {
	const WISHLISTS = 'wishlists';
	const CREATE    = 'create_list';

	protected $wrapper_tag        = 'div';
	protected $wrapper_classes    = [ 'bc-account-page', 'bc-account-wish-lists' ];
	protected $wrapper_attributes = [ 'data-js' => 'account-wish-lists-list' ];

	protected $template = 'components/wishlist/list.php';

	protected function parse_options( array $options ) {
		$defaults = [
			self::WISHLISTS => [],
		];

		return wp_parse_args( $options, $defaults );
	}

	public function get_data() {
		$lists = $this->options[ self::WISHLISTS ];
		if ( ! is_array( $lists ) ) {
			$lists = [];
		}

		return [
			self::WISHLISTS => array_map( [ $this, 'render_list_row' ], $lists ),
			self::CREATE    => $this->create_list_form(),
		];
	}

	private function render_list_row( Wishlist $wishlist ) {
		$component = Wishlist_List_Row::factory( [
			Wishlist_List_Row::WISHLIST => $wishlist,
		] );

		return $component->render();
	}

	private function create_list_form() {
		$component = Wishlist_New_Button::factory();

		return $component->render();
	}


}
