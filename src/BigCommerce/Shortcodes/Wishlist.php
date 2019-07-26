<?php


namespace BigCommerce\Shortcodes;

use BigCommerce\Accounts\Customer;
use BigCommerce\Accounts\Wishlists\Wishlist as Account_Wishlist;
use BigCommerce\Api\v3\Api\WishlistsApi;
use BigCommerce\Api\v3\ApiException;
use BigCommerce\Api\v3\Model\Wishlist as Api_Wishlist;
use BigCommerce\Settings\Sections\Wishlists as Wishlist_Settings;
use BigCommerce\Templates\Wishlist_Detail;
use BigCommerce\Templates\Wishlist_List;
use BigCommerce\Templates\Wishlist_Not_Available;

class Wishlist implements Shortcode {
	const NAME       = 'bigcommerce_wish_lists';
	const LIST_PARAM = 'list';

	/** @var WishlistsApi */
	private $wishlists;

	public function __construct( WishlistsApi $wishlists ) {
		$this->wishlists = $wishlists;
	}

	public function render( $attr, $instance ) {
		if ( ! get_option( Wishlist_Settings::ENABLED ) ) {
			return '';
		}

		$user_id = get_current_user_id();
		if ( empty( $user_id ) ) {
			return $this->no_customer_template();
		}
		$customer    = new Customer( $user_id );
		$customer_id = $customer->get_customer_id();
		if ( empty( $customer_id ) ) {
			return $this->no_customer_template();
		}

		$list = filter_input( INPUT_GET, self::LIST_PARAM, FILTER_VALIDATE_INT );

		if ( $list ) {
			try {
				$wishlist = $this->wishlists->getWishlist( $list )->getData();
				if ( (int) $wishlist->getCustomerId() !== $customer_id ) {
					return $this->not_found_template();
				}
				$controller = Wishlist_Detail::factory( [
					Wishlist_Detail::WISHLIST => new Account_Wishlist( $wishlist ),
				] );
				return $controller->render();
			} catch ( ApiException $e ) {
				return $this->not_found_template();
			}
		}

		try {
			$wishlists = array_map( function( Api_Wishlist $wishlist ) {
				return new Account_Wishlist( $wishlist );
			}, $this->wishlists->listWishlists( [ 'customer_id' => $customer_id ] )->getData() );
		} catch ( ApiException $e ) {
			$wishlists = [];
		}

		$controller = Wishlist_List::factory( [
			Wishlist_List::WISHLISTS => $wishlists,
		] );

		return $controller->render();
	}

	private function no_customer_template() {
		$controller = Wishlist_Not_Available::factory( [
			Wishlist_Not_Available::MESSAGE => __( 'Unable to load Wish Lists.', 'bigcommerce' ),
		] );

		return $controller->render();
	}

	private function not_found_template() {
		$controller = Wishlist_Not_Available::factory();

		return $controller->render();
	}


}