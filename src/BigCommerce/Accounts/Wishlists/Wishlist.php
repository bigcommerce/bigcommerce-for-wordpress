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
 * A wrapper around API wishlists to manage wishlist data, generate URLs for wishlist actions,
 * and handle items in the wishlist.
 */
class Wishlist {
	/** @var \BigCommerce\Api\v3\Model\Wishlist The underlying API wishlist object */
	private $wishlist;

	/**
	 * Wishlist constructor.
	 *
	 * @param \BigCommerce\Api\v3\Model\Wishlist $wishlist The wishlist API model
	 */
	public function __construct( \BigCommerce\Api\v3\Model\Wishlist $wishlist ) {
		$this->wishlist = $wishlist;
	}

	/**
	 * Get the ID of the wishlist.
	 *
	 * @return int The ID of the wishlist.
	 */
	public function list_id() {
		return $this->wishlist->getId();
	}

	/**
	 * Get the customer ID associated with the wishlist.
	 *
	 * @return int The customer ID.
	 */
	public function customer_id() {
		return $this->wishlist->getCustomerId();
	}

	/**
	 * Get the token associated with the wishlist.
	 *
	 * @return string The wishlist token.
	 */
	public function token() {
		return $this->wishlist->getToken();
	}

	/**
	 * Get the name of the wishlist.
	 *
	 * @return string The name of the wishlist.
	 */
	public function name() {
		return $this->wishlist->getName();
	}

	/**
	 * Get the number of items in the wishlist.
	 *
	 * @return int The number of items.
	 */
	public function count() {
		return count( $this->wishlist->getItems() );
	}

	/**
	 * Get the underlying API wishlist object.
	 *
	 * @return \BigCommerce\Api\v3\Model\Wishlist The wishlist API model.
	 */
	public function wishlist() {
		return $this->wishlist;
	}

	/**
	 * Get the product IDs of items in the wishlist.
	 *
	 * @return int[] Array of product IDs.
	 */
	public function items() {
		$product_ids = array_map( function ( WishlistItem $item ) {
			return $item->getProductId();
		}, $this->wishlist->getItems() );
		$product_ids = array_filter( array_map( 'intval', $product_ids ) );
		/**
		 * Filter the product IDs in the wishlist
		 *
		 * @param int[]    $product_ids The list of product IDs.
		 * @param Wishlist $wishlist The wishlist object.
		 */
		$product_ids = apply_filters( 'bigcommerce/wishlist/items', $product_ids, $this );

		return $product_ids;
	}

	/**
	 * Get the raw item objects from the API response.
	 *
	 * @return WishlistItem[] Array of raw wishlist item objects.
	 */
	public function raw_items() {
		return $this->wishlist->getItems();
	}

	/**
	 * Check if the wishlist is publicly shared.
	 *
	 * @return bool True if the wishlist is public, false otherwise.
	 */
	public function is_public() {
		return (bool) $this->wishlist->getIsPublic();
	}

	/**
	 * Get the public URL to view the wishlist.
	 *
	 * @return string The URL to view the wishlist.
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
		 * @param string   $url      The wishlist URL.
		 * @param Wishlist $wishlist The wishlist object.
		 */
		return apply_filters( 'bigcommerce/wishlist/public-url', $url, $this );
	}

	/**
	 * Get the user's private URL to view the wishlist.
	 *
	 * @return string The user's private wishlist URL.
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
		 * Filter the URL for a user to manage a wishlist.
		 *
		 * @param string   $url      The wishlist URL.
		 * @param Wishlist $wishlist The wishlist object.
		 */
		return apply_filters( 'bigcommerce/wishlist/user-url', $url, $this );
	}

	/**
	 * Get the URL to handle update requests for this wishlist.
	 *
	 * @return string The URL for wishlist updates.
	 */
	public function edit_url() {
		$url = home_url( sprintf( 'bigcommerce/%s/%s/%d', Request_Router::ACTION, Edit_Wishlist::ACTION, $this->list_id() ) );

		/**
		 * Filter the URL for posting an update to a wishlist's settings.
		 *
		 * @param string   $url      The form handler URL.
		 * @param Wishlist $wishlist The wishlist object.
		 */
		return apply_filters( 'bigcommerce/wishlist/edit-url', $url, $this );
	}

	/**
	 * Get the URL to delete the wishlist.
	 *
	 * @return string The URL to delete the wishlist.
	 */
	public function delete_url() {
		$url = home_url( sprintf( 'bigcommerce/%s/%s/%d', Request_Router::ACTION, Delete_Wishlist::ACTION, $this->list_id() ) );

		/**
		 * Filter the URL for deleting a wishlist.
		 *
		 * @param string   $url      The form handler URL.
		 * @param Wishlist $wishlist The wishlist object.
		 */
		return apply_filters( 'bigcommerce/wishlist/delete-url', $url, $this );
	}

	/**
	 * Get the URL to add an item to the wishlist.
	 *
	 * @param int $product_id The ID of the product to add.
	 *
	 * @return string The URL for adding an item to the wishlist.
	 */
	public function add_item_url( $product_id ) {
		$url = home_url( sprintf( 'bigcommerce/%s/%s/%d', Request_Router::ACTION, Add_Item::ACTION, $this->list_id() ) );
		$url = add_query_arg( [ 'product_id' => $product_id ], $url );
		$url = wp_nonce_url( $url, sprintf( '%s/%d/%d', Add_Item::ACTION, $this->list_id(), $product_id ) );

		/**
		 * Filter the URL for adding an item to a wishlist.
		 *
		 * @param string   $url        The form handler URL.
		 * @param Wishlist $wishlist   The wishlist object.
		 * @param int      $product_id The ID of the product to add.
		 */
		return apply_filters( 'bigcommerce/wishlist/add-item-url', $url, $this, $product_id );
	}

	/**
	 * Get the URL to remove an item from the wishlist.
	 *
	 * @param int $product_id The ID of the product to remove.
	 *
	 * @return string The URL for removing an item from the wishlist.
	 */
	public function delete_item_url( $product_id ) {
		$url = home_url( sprintf( 'bigcommerce/%s/%s/%d', Request_Router::ACTION, Remove_Item::ACTION, $this->list_id() ) );
		$url = add_query_arg( [ 'product_id' => $product_id ], $url );
		$url = wp_nonce_url( $url, sprintf( '%s/%d/%d', Remove_Item::ACTION, $this->list_id(), $product_id ) );

		/**
		 * Filter the URL for removing an item from a wishlist.
		 *
		 * @param string   $url        The form handler URL.
		 * @param Wishlist $wishlist   The wishlist object.
		 * @param int      $product_id The ID of the product to remove.
		 */
		return apply_filters( 'bigcommerce/wishlist/remove-item-url', $url, $this, $product_id );
	}

	/**
	 * Get the URL to create a wishlist.
	 *
	 * @return string The URL to create a new wishlist.
	 */
	public static function create_url() {
		$url = home_url( sprintf( 'bigcommerce/%s/%s', Request_Router::ACTION, Create_Wishlist::ACTION ) );

		/**
		 * Filter the URL for creating a wishlist.
		 *
		 * @param string $url The form handler URL.
		 */
		return apply_filters( 'bigcommerce/wishlist/create-url', $url );
	}
}