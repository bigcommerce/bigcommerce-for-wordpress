<?php


namespace BigCommerce\Templates;

use BigCommerce\Accounts\Wishlists\Wishlist;

class Wishlist_Add_Item extends Controller {
	const PRODUCT_ID = 'product_id';
	const WISHLISTS  = 'wishlists';
	const HEADING    = 'heading';
	const LINKS      = 'links';
	const CREATE     = 'create_list';

	protected $template = 'components/wishlist/add-item.php';
	protected $wrapper_tag = 'div';
	protected $wrapper_classes = [ 'bc-pdp-wish-list-wrapper' ];
	protected $wrapper_attributes = [ 'data-js' => 'bc-pdp-add-to-wish-list' ];

	protected function parse_options( array $options ) {
		$defaults = [
			self::HEADING    => __( 'Add to Wish List', 'bigcommerce' ),
			self::PRODUCT_ID => get_the_ID(),
			self::WISHLISTS  => [],
		];

		return wp_parse_args( $options, $defaults );
	}

	public function get_data() {
		return [
			self::HEADING => $this->options[ self::HEADING ],
			self::LINKS   => $this->build_links( $this->options[ self::PRODUCT_ID ], $this->options[ self::WISHLISTS ] ),
			self::CREATE  => $this->create_list_form( $this->options[ self::PRODUCT_ID ] ),
		];
	}

	/**
	 * @param int        $product_id
	 * @param Wishlist[] $wishlists
	 *
	 * @return array
	 */
	private function build_links( $product_id, $wishlists ) {
		$links = array_map( function ( Wishlist $wishlist ) use ( $product_id ) {
			return [
				'label' => $wishlist->name(),
				'url'   => $wishlist->add_item_url( $product_id ),
			];
		}, $wishlists );

		return $links;
	}

	private function create_list_form( $product_id ) {
		$component = Wishlist_New_Link::factory( [
			Wishlist_New_Link::LABEL    => __( 'Create New List', 'bigcommerce' ),
			Wishlist_New_Link::PRODUCTS => [ $product_id ],
		] );

		return $component->render();
	}

}
