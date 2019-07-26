<?php


namespace BigCommerce\Accounts\Wishlists;

use BigCommerce\Accounts\Customer;
use BigCommerce\Accounts\Wishlists\Wishlist as Account_Wishlist;
use BigCommerce\Api\v3\Api\WishlistsApi;
use BigCommerce\Api\v3\ApiException;
use BigCommerce\Api\v3\Model\Wishlist as Api_Wishlist;
use BigCommerce\Settings\Sections\Wishlists as Wishlist_Settings;
use BigCommerce\Templates\Product_Single;
use BigCommerce\Templates\Wishlist_Add_Item;

/**
 * Class Add_Item_View
 *
 * Adds the "Add to Wish List" template after the purchase form
 * on the product single view
 */
class Add_Item_View {
	/** @var WishlistsApi */
	private $wishlists;

	public function __construct( WishlistsApi $wishlists ) {
		$this->wishlists = $wishlists;
	}

	/**
	 * @param array  $data
	 * @param string $template
	 * @param array  $options
	 *
	 * @return array
	 * @filter bigcommerce/template=components/products/product-single.php/data
	 */
	public function filter_product_single_template( $data, $template, $options ) {
		if ( ! get_option( Wishlist_Settings::ENABLED ) || ! is_user_logged_in() ) {
			return $data;
		}
		$customer    = new Customer( get_current_user_id() );
		$customer_id = $customer->get_customer_id();
		if ( empty( $customer_id ) ) {
			return $data;
		}
		$wishlists  = $this->get_wishlists( $customer_id );
		$controller = Wishlist_Add_Item::factory( [
			Wishlist_Add_Item::PRODUCT_ID => $data[ Product_Single::PRODUCT ]->bc_id(),
			Wishlist_Add_Item::WISHLISTS  => $wishlists,
		] );

		$data[ Product_Single::FORM ] .= $controller->render();

		return $data;
	}

	/**
	 * @param int $customer_id
	 *
	 * @return Account_Wishlist[]
	 */
	private function get_wishlists( $customer_id ) {
		try {
			return array_map( function ( Api_Wishlist $wishlist ) {
				return new Account_Wishlist( $wishlist );
			}, $this->wishlists->listWishlists( [ 'customer_id' => $customer_id ] )->getData() );
		} catch ( ApiException $e ) {
			return [];
		}
	}
}