<?php


namespace BigCommerce\Templates;

use BigCommerce\Accounts\Wishlists\Wishlist;
use BigCommerce\Exceptions\Product_Not_Found_Exception;
use BigCommerce\Post_Types\Product\Product;

class Wishlist_Detail extends Controller {
	const WISHLIST   = 'wishlist';
	const PRODUCTS   = 'products';
	const BREADCRUMB = 'breadcrumb';
	const HEADER     = 'header';

	protected $wrapper_tag = 'div';
	protected $wrapper_classes = [ 'bc-manage-wish-list-wrapper' ];
	protected $wrapper_attributes = [ 'data-js' => 'bc-manage-wish-list' ];

	protected $template = 'components/wishlist/detail.php';

	protected function parse_options( array $options ) {
		$defaults = [
			self::WISHLIST => null,
		];

		return wp_parse_args( $options, $defaults );
	}

	public function get_data() {
		return [
			self::WISHLIST   => $this->options[ self::WISHLIST ],
			self::PRODUCTS   => $this->render_products( $this->options[ self::WISHLIST ] ),
			self::BREADCRUMB => $this->render_breadcrumb( $this->options[ self::WISHLIST ] ),
			self::HEADER     => $this->render_header( $this->options[ self::WISHLIST ] ),
		];
	}

	private function render_products( Wishlist $wishlist ) {
		return array_filter( array_map( function ( $product_id ) use ( $wishlist ) {
			try {
				$product    = Product::by_product_id( $product_id );
				$controller = Wishlist_Product::factory( [
					Wishlist_Product::WISHLIST => $wishlist,
					Wishlist_Product::PRODUCT  => $product,
				] );

				return $controller->render();
			} catch ( Product_Not_Found_Exception $e ) {
				return '';
			}
		}, $wishlist->items() ) );
	}

	private function render_breadcrumb( Wishlist $wishlist ) {
		$controller = Wishlist_Detail_Breadcrumb::factory( [
			Wishlist_Detail_Breadcrumb::WISHLIST => $wishlist,
		] );

		return $controller->render();
	}

	private function render_header( Wishlist $wishlist ) {
		$controller = Wishlist_Detail_Header::factory( [
			Wishlist_Detail_Header::WISHLIST => $wishlist,
		] );

		return $controller->render();
	}

}
