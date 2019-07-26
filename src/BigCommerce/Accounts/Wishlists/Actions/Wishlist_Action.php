<?php


namespace BigCommerce\Accounts\Wishlists\Actions;

use BigCommerce\Accounts\Customer;
use BigCommerce\Accounts\Wishlists\Wishlist;
use BigCommerce\Api\v3\Api\WishlistsApi;

abstract class Wishlist_Action {

	/** @var WishlistsApi */
	protected $wishlists;

	public function __construct( WishlistsApi $wishlists ) {
		$this->wishlists = $wishlists;
	}

	abstract public function handle_request( $args );

	abstract protected function sanitize_request( array $args, array $submission );

	/**
	 * Get the wishlist for the customer
	 *
	 * @param int $user_id
	 * @param int $wishlist_id
	 *
	 * @return Wishlist
	 */
	protected function get_customer_wishlist( $user_id, $wishlist_id ) {
		try {
			$wishlist = $this->wishlists->getWishlist( $wishlist_id )->getData();
		} catch ( \Exception $e ) {
			throw new \RuntimeException( __( 'Wish List not found', 'bigcommerce' ), 404 );
		}

		if ( (int) $wishlist->getCustomerId() !== $this->get_customer_id( $user_id ) ) {
			throw new \InvalidArgumentException( __( 'Wish List not found', 'bigcommerce' ), 404 );
		}

		return new Wishlist( $wishlist );
	}

	/**
	 * Get the Customer ID for the user
	 *
	 * @param int $user_id
	 *
	 * @return int
	 */
	protected function get_customer_id( $user_id ) {
		$customer = new Customer( $user_id );

		return $customer->get_customer_id();
	}
}
