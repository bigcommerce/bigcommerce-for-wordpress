<?php


namespace BigCommerce\Accounts\Wishlists;

use BigCommerce\Accounts\Customer;
use BigCommerce\Accounts\Wishlists\Wishlist as Account_Wishlist;
use BigCommerce\Api\v3\Api\WishlistsApi;
use BigCommerce\Api\v3\ApiException;
use BigCommerce\Api\v3\Model\Wishlist as Api_Wishlist;
use BigCommerce\Import\Processors\Storefront_Processor;
use BigCommerce\Settings\Sections\Wishlists as Wishlist_Settings;
use BigCommerce\Taxonomies\Channel\Channel;
use BigCommerce\Templates\Product_Single;
use BigCommerce\Templates\Wishlist_Add_Item;

/**
 * Class Add_Item_View
 *
 * Adds the "Add to Wish List" template after the purchase form on the product single view.
 * This class manages the display of the "Add to Wishlist" button and the wishlist functionality
 * on product detail pages. It checks whether the feature is enabled, if the user is logged in,
 * and then allows the user to add a product to an existing wishlist.
 *
 * @package BigCommerce\Accounts\Wishlists
 */
class Add_Item_View {
	/** @var WishlistsApi */
	private $wishlists;

    /**
     * Add_Item_View constructor.
     *
     * Initializes the Add_Item_View class with the provided WishlistsApi instance, which is used
     * to interact with the BigCommerce API to manage wishlists.
     *
     * @param WishlistsApi $wishlists The WishlistsApi instance to interact with BigCommerce wishlists.
     */
	public function __construct( WishlistsApi $wishlists ) {
		$this->wishlists = $wishlists;
	}

    /**
     * Filters the data of the product single template to add the "Add to Wish List" button.
     *
     * This method checks if the "Add to Wishlist" feature is enabled, if the user is logged in, 
     * and if the customer has a wishlist. If all conditions are met, it adds the "Add to Wish List" 
     * button to the product single template.
     *
     * @param array  $data The template data for the product single page.
     * @param string $template The name of the template file.
     * @param array  $options Additional options for rendering the template.
     *
     * @return array The modified template data with the "Add to Wish List" button included.
     * @filter bigcommerce/template=components/products/product-single.php/data
     */
	public function filter_product_single_template( $data, $template, $options ) {
		if ( ! Channel::is_msf_channel_prop_on( Storefront_Processor::SHOW_ADD_TO_WISHLIST ) || ! get_option( Wishlist_Settings::ENABLED ) || ! is_user_logged_in() ) {
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
     * Fetches the customer's wishlists from the API.
     *
     * This method retrieves a list of the customer's existing wishlists and returns them as 
     * an array of Account_Wishlist objects.
     *
     * @param int $customer_id The ID of the customer to fetch wishlists for.
     *
     * @return Account_Wishlist[] An array of Account_Wishlist objects representing the customer's wishlists.
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
