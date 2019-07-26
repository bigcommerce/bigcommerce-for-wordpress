<?php


namespace BigCommerce\Accounts\Wishlists;

use BigCommerce\Accounts\Wishlists\Actions\Add_Item;
use BigCommerce\Accounts\Wishlists\Actions\Create_Wishlist;
use BigCommerce\Accounts\Wishlists\Actions\Delete_Wishlist;
use BigCommerce\Accounts\Wishlists\Actions\Edit_Wishlist;
use BigCommerce\Accounts\Wishlists\Actions\Remove_Item;
use BigCommerce\Accounts\Wishlists\Actions\Request_Router;
use BigCommerce\Api\v3\Model\WishlistItem;
use BigCommerce\Pages\Wishlist_Page;
use BigCommerce\Post_Types\Product\Product;
use BigCommerce\Shortcodes;

/**
 * Class Wishlist
 *
 * A wrapper around API wishlists
 */
class Wishlist {
	/** @var \BigCommerce\Api\v3\Model\Wishlist */
	private $wishlist;

	public function __construct( \BigCommerce\Api\v3\Model\Wishlist $wishlist ) {
		$this->wishlist = $wishlist;
	}

	public function list_id() {
		return $this->wishlist->getId();
	}

	public function customer_id() {
		return $this->wishlist->getCustomerId();
	}

	public function token() {
		return $this->wishlist->getToken();
	}

	/**
	 * Get the name of the wishlist
	 *
	 * @return string
	 */
	public function name() {
		return $this->wishlist->getName();
	}

	/**
	 * Get the number of items in the wishlist
	 *
	 * @return int
	 */
	public function count() {
		return count( $this->wishlist->getItems() );
	}

	/**
	 * Get the underlying API wishlist
	 *
	 * @return \BigCommerce\Api\v3\Model\Wishlist
	 */
	public function wishlist() {
		return $this->wishlist;
	}

	/**
	 * Get the items in the wishlist
	 *
	 * @return int[]
	 */
	public function items() {
		$product_ids = array_map( function ( WishlistItem $item ) {
			return $item->getProductId();
		}, $this->wishlist->getItems() );
		$product_ids = array_filter( array_map( 'intval', $product_ids ) );
		/**
		 * Filter the product IDs in the wishlist
		 *
		 * @param int[]    $product_ids
		 * @param Wishlist $wishlist
		 */
		$product_ids = apply_filters( 'bigcommerce/wishlist/items', $product_ids, $this );

		return $product_ids;
	}

	/**
	 * Get the raw item objects from the API response
	 *
	 * @return WishlistItem[]
	 */
	public function raw_items() {
		return $this->wishlist->getItems();
	}

	/**
	 * Checks if the list is publicly shared
	 *
	 * @return bool
	 */
	public function is_public() {
		return (bool) $this->wishlist->getIsPublic();
	}

	/**
	 * Get the public URL to view the wishlist
	 *
	 * @return string
	 */
	public function public_url() {
		if ( ! $this->is_public() ) {
			return '';
		}

		$url = add_query_arg( [
			Wishlist_Request_Parser::LIST_PARAM  => $this->list_id(),
			Wishlist_Request_Parser::TOKEN_PARAM => $this->token(),
		], get_post_type_archive_link( Product::NAME ) );

		/**
		 * Filter the URL for a public wishlist
		 *
		 * @param string   $url      The wishlist URL
		 * @param Wishlist $wishlist The wishlist object
		 */
		return apply_filters( 'bigcommerce/wishlist/public-url', $url, $this );
	}

	/**
	 * Get the user's private URL to view the wishlist
	 *
	 * @return string
	 */
	public function user_url() {
		$page_id = get_option( Wishlist_Page::NAME, 0 );
		if ( empty( $page_id ) || get_post_status( $page_id ) !== 'publish' ) {
			return home_url();
		}
		$url = add_query_arg( [
			Shortcodes\Wishlist::LIST_PARAM => $this->list_id(),
		], get_permalink( $page_id ) );

		/**
		 * Filter the URL for a user to manage a wishlist
		 *
		 * @param string   $url      The wishlist URL
		 * @param Wishlist $wishlist The wishlist object
		 */
		return apply_filters( 'bigcommerce/wishlist/user-url', $url, $this );
	}

	/**
	 * Get the URL to handle update requests for this wishlist
	 *
	 * @return string
	 */
	public function edit_url() {
		$url = home_url( sprintf( 'bigcommerce/%s/%s/%d', Request_Router::ACTION, Edit_Wishlist::ACTION, $this->list_id() ) );

		/**
		 * Filter the URL for posting an update to a wishlist's settings
		 *
		 * @param string   $url      The form handler URL
		 * @param Wishlist $wishlist The wishlist object
		 */
		return apply_filters( 'bigcommerce/wishlist/edit-url', $url, $this );
	}

	/**
	 * Get the URL to delete the wishlist
	 *
	 * @return string
	 */
	public function delete_url() {
		$url = home_url( sprintf( 'bigcommerce/%s/%s/%d', Request_Router::ACTION, Delete_Wishlist::ACTION, $this->list_id() ) );

		/**
		 * Filter the URL for deleting a wishlist
		 *
		 * @param string   $url      The form handler URL
		 * @param Wishlist $wishlist The wishlist object
		 */
		return apply_filters( 'bigcommerce/wishlist/delete-url', $url, $this );
	}

	public function add_item_url( $product_id ) {
		$url = home_url( sprintf( 'bigcommerce/%s/%s/%d', Request_Router::ACTION, Add_Item::ACTION, $this->list_id() ) );
		$url = add_query_arg( [ 'product_id' => $product_id ], $url );
		$url = wp_nonce_url( $url, sprintf( '%s/%d/%d', Add_Item::ACTION, $this->list_id(), $product_id ) );

		/**
		 * Filter the URL for adding an item to a wishlist
		 *
		 * @param string   $url        The form handler URL
		 * @param Wishlist $wishlist   The wishlist object
		 * @param int      $product_id The ID of the product to remove
		 */
		return apply_filters( 'bigcommerce/wishlist/add-item-url', $url, $this, $product_id );
	}

	public function delete_item_url( $product_id ) {
		$url = home_url( sprintf( 'bigcommerce/%s/%s/%d', Request_Router::ACTION, Remove_Item::ACTION, $this->list_id() ) );
		$url = add_query_arg( [ 'product_id' => $product_id ], $url );
		$url = wp_nonce_url( $url, sprintf( '%s/%d/%d', Remove_Item::ACTION, $this->list_id(), $product_id ) );

		/**
		 * Filter the URL for removing an item from a wishlist
		 *
		 * @param string   $url        The form handler URL
		 * @param Wishlist $wishlist   The wishlist object
		 * @param int      $product_id The ID of the product to remove
		 */
		return apply_filters( 'bigcommerce/wishlist/remove-item-url', $url, $this, $product_id );
	}

	public static function create_url() {
		$url = home_url( sprintf( 'bigcommerce/%s/%s', Request_Router::ACTION, Create_Wishlist::ACTION ) );

		/**
		 * Filter the URL for creating a wishlist
		 *
		 * @param string $url The form handler URL
		 */
		return apply_filters( 'bigcommerce/wishlist/create-url', $url );
	}
}