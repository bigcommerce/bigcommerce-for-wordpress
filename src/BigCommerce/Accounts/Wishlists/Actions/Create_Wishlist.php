<?php


namespace BigCommerce\Accounts\Wishlists\Actions;

use BigCommerce\Accounts\Wishlists\Wishlist;
use BigCommerce\Api\v3\Model\WishlistItem;
use BigCommerce\Api\v3\Model\WishlistRequest;
use BigCommerce\Pages\Wishlist_Page;

/**
 * Handles the logic for creating a new wishlist for a customer.
 * 
 * This class processes the request to create a wishlist, including validating and sanitizing
 * the request data, creating a wishlist, and redirecting the user to the newly created wishlist's page.
 * It also handles errors and triggers appropriate actions upon success or failure.
 *
 * @package BigCommerce\Accounts\Wishlists\Actions
 */
class Create_Wishlist extends Wishlist_Action {
	/**
	 * The action identifier for creating a wishlist.
	 *
	 * This constant is used to identify the specific action for creating a wishlist.
	 *
	 * @var string
	 */
	const ACTION = 'create';

	/**
	 * Handles the request to create a new wishlist.
	 *
	 * This method validates the incoming data, sanitizes it, and creates a new wishlist using
	 * the provided information. Upon successful creation, the user is redirected to the wishlist's page.
	 * If an error occurs, an error message is triggered.
	 *
	 * @param array $args The arguments from the request.
	 * 
	 * @return void
	 */
	public function handle_request( $args ) {
		$redirect = get_the_permalink( get_option( Wishlist_Page::NAME, 0 ) );
		try {
			$submission = $this->sanitize_request( $args, $_POST );
			$request    = new WishlistRequest( [
				'customer_id' => $this->get_customer_id( get_current_user_id() ),
				'name'        => $submission['name'],
				'is_public'   => $submission['public'],
				'items'       => array_map( function ( $product_id ) {
					return new WishlistItem( [ 'product_id' => $product_id ] );
				}, $submission['items'] ),
			] );
			$response   = $this->wishlists->createWishlist( $request );

			$wishlist = new Wishlist( $response->getData() );
			$redirect = $wishlist->user_url();
			/**
			 * Triggers success notification after creating a new wishlist.
			 *
			 * @param string $message The success message to display
			 * @param array $submission The submitted wishlist data
			 * @param string $redirect The URL to redirect to after creation
			 * @param array $data Additional data passed to the action
			 */
			do_action( 'bigcommerce/form/success', __( 'Wish List created', 'bigcommerce' ), $submission, $redirect, [] );
		} catch ( \Exception $e ) {
			/**
			 * Triggers error notification if wishlist creation fails.
			 *
			 * @param \WP_Error $error The error details
			 * @param array $submission The submitted wishlist data
			 * @param string $redirect The URL to redirect to after error
			 * @param array $data Additional data passed to the action
			 */
			do_action( 'bigcommerce/form/error', new \WP_Error( $e->getCode(), $e->getMessage() ), $_POST, $redirect, [] );
		}
	}

    /**
     * Validates and sanitizes the incoming request to create a wishlist.
     *
     * This method processes the submission data, ensuring required fields are present and sanitized.
     * It throws exceptions for invalid or missing data and returns a sanitized array of the request data.
     *
     * @param array $args The arguments from the request.
     * @param array $submission The submission data from the request.
     * 
     * @return array The sanitized request data, including the wishlist name, public status, and product items.
     * 
     * @throws \InvalidArgumentException If the request is missing required fields or contains invalid data.
     */
	protected function sanitize_request( array $args, array $submission ) {
		$submission = filter_var_array( $submission, [
			'_wpnonce' => FILTER_SANITIZE_STRING,
			'name'     => FILTER_SANITIZE_STRING,
			'public'   => FILTER_VALIDATE_BOOLEAN,
			'items'    => FILTER_SANITIZE_STRING,
		] );

		if ( empty( $submission['_wpnonce'] ) || ! wp_verify_nonce( $submission['_wpnonce'], self::ACTION ) ) {
			throw new \InvalidArgumentException( __( 'Invalid request. Please try again.', 'bigcommerce' ), 403 );
		}

		if ( empty( $submission['name'] ) ) {
			throw new \InvalidArgumentException( __( 'Missing Wish List name', 'bigcommerce' ), 400 );
		}

		return [
			'name'   => wp_unslash( sanitize_text_field( $submission['name'] ) ),
			'public' => ! empty( $submission['public'] ),
			'items'  => array_filter( array_map( 'intval', explode( ',', $submission['items'] ) ) ),
		];
	}
}
