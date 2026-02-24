<?php


namespace BigCommerce\Accounts\Wishlists\Actions;

use BigCommerce\Accounts\Customer;
use BigCommerce\Accounts\Wishlists\Wishlist;
use BigCommerce\Api\v3\Api\WishlistsApi;

/**
 * Abstract class for handling wishlist-related actions.
 *
 * This class provides the base structure for actions that involve wishlists, such as creating, updating, or deleting 
 * wishlists. It defines common functionality for working with customer wishlists and handling requests, which must 
 * be extended by specific actions.
 *
 * @package BigCommerce\Accounts\Wishlists\Actions
 */
abstract class Wishlist_Action {

	/** @var WishlistsApi */
	protected $wishlists;

    /**
     * Wishlist_Action constructor.
     *
     * Initializes the action with the WishlistsApi instance, allowing the action to interact with the BigCommerce API
     * for wishlist management.
     *
     * @param WishlistsApi $wishlists The WishlistsApi instance used for wishlist operations.
     */
	public function __construct( WishlistsApi $wishlists ) {
		$this->wishlists = $wishlists;
	}

    /**
     * Handle the incoming request.
     *
     * This is an abstract method that must be implemented by subclasses to handle the specific request for a wishlist action
     * (e.g., create, update, delete). The method should process the request and return the appropriate response.
     *
     * @param mixed $args The arguments for the action request, typically including the wishlist ID and any necessary data.
     *
     * @return void
     */
	abstract public function handle_request( $args );

    /**
     * Sanitize and validate the request data.
     *
     * This is an abstract method that must be implemented by subclasses to sanitize and validate the request submission
     * before performing any operations on the wishlist.
     *
     * @param array $args The arguments for the action request.
     * @param array $submission The submitted form data, typically from a POST request.
     *
     * @return array Sanitized data to be used in the action handler.
     */
	abstract protected function sanitize_request( array $args, array $submission );

    /**
     * Get the wishlist for the customer.
     *
     * Fetches the wishlist for a given customer based on their user ID and wishlist ID. If the wishlist is not found or
     * does not belong to the customer, an exception is thrown.
     *
     * @param int $user_id The ID of the user (customer).
     * @param int $wishlist_id The ID of the wishlist to retrieve.
     *
     * @return Wishlist The customer's wishlist.
     * @throws \RuntimeException If the wishlist cannot be found or accessed.
     * @throws \InvalidArgumentException If the wishlist does not belong to the customer.
     */
	protected function get_customer_wishlist( $user_id, $wishlist_id ) {
		try {
			$wishlist = $this->wishlists->getWishlist( $wishlist_id )->getData();
		} catch ( \Exception $e ) {
			throw new \RuntimeException( __( 'Wish List not found', 'bigcommerce' ), 404 );
		}

		// Check if the wishlist belongs to the customer
		if ( (int) $wishlist->getCustomerId() !== $this->get_customer_id( $user_id ) ) {
			throw new \InvalidArgumentException( __( 'Wish List not found', 'bigcommerce' ), 404 );
		}

		return new Wishlist( $wishlist );
	}

    /**
     * Get the Customer ID for the user.
     *
     * Retrieves the customer ID associated with the given user ID.
     *
     * @param int $user_id The ID of the user (customer).
     *
     * @return int The customer ID associated with the user.
     */
	protected function get_customer_id( $user_id ) {
		$customer = new Customer( $user_id );

		return $customer->get_customer_id();
	}
}
