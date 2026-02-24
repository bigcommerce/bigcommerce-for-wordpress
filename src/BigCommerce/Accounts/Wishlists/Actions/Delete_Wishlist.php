<?php


namespace BigCommerce\Accounts\Wishlists\Actions;

use BigCommerce\Pages\Wishlist_Page;

/**
 * Handles the logic for deleting a wishlist.
 *
 * This class processes the request to delete a specific wishlist for a customer. It validates
 * the provided wishlist ID, deletes the wishlist, and redirects the user to the default wishlist page.
 * It also triggers success or error actions based on the outcome of the operation.
 *
 * @package BigCommerce\Accounts\Wishlists\Actions
 */
class Delete_Wishlist extends Wishlist_Action {
	/**
	 * The action identifier for deleting a wishlist.
	 *
	 * This constant is used to identify the specific action for deleting a wishlist.
	 *
	 * @var string
	 */
	const ACTION = 'delete';

    /**
     * Handles the request to delete a specific wishlist.
     *
     * This method validates the request, retrieves the wishlist using the provided ID, and
     * deletes the wishlist from the system. Upon successful deletion, the user is redirected
     * to the wishlist page with a success message. If an error occurs, an error message is triggered.
     *
     * @param array $args The arguments from the request.
     * 
     * @return void
     */
	public function handle_request( $args ) {
		$redirect = get_the_permalink( get_option( Wishlist_Page::NAME, 0 ) );
		try {
			$submission = $this->sanitize_request( $args, $_POST );
			$wishlist   = $this->get_customer_wishlist( get_current_user_id(), $submission['id'] );
			$this->wishlists->deleteWishlist( $wishlist->list_id() );

			/**
			 * Trigger success message after deleting wishlist.
			 *
			 * @param string $message Success message to display
			 * @param array $submission The submitted form data
			 * @param string $redirect The URL to redirect to
			 * @param array $data Additional data
			 */
			do_action( 'bigcommerce/form/success', __( 'Wish List deleted', 'bigcommerce' ), $submission, $redirect, [] );
		} catch ( \Exception $e ) {
			/**
			 * Trigger error message if deleting wishlist fails.
			 *
			 * @param \WP_Error $error The error object
			 * @param array $submission The submitted form data
			 * @param string $redirect The URL to redirect to
			 * @param array $data Additional data
			 */
			do_action( 'bigcommerce/form/error', new \WP_Error( $e->getCode(), $e->getMessage() ), $_POST, $redirect, [] );
		}
	}

    /**
     * Validates and sanitizes the request to delete a wishlist.
     *
     * This method processes the submission data, ensuring the wishlist ID is valid and sanitizing
     * the request for security. It throws exceptions for invalid or missing data and returns a sanitized
     * array of the request data.
     *
     * @param array $args The arguments from the request.
     * @param array $submission The submission data from the request.
     * 
     * @return array The sanitized request data, including the wishlist ID.
     * 
     * @throws \InvalidArgumentException If the request is missing required fields or contains invalid data.
     */
	protected function sanitize_request( array $args, array $submission ) {
		$wishlist_id = reset( $args );
		if ( empty( $wishlist_id ) || ! is_numeric( $wishlist_id ) ) {
			throw new \InvalidArgumentException( __( 'Invalid Wish List ID', 'bigcommerce' ), 400 );
		}

		$submission = filter_var_array( $submission, [
			'_wpnonce' => FILTER_SANITIZE_STRING,
		] );

		if ( empty( $submission['_wpnonce'] ) || ! wp_verify_nonce( $submission['_wpnonce'], self::ACTION . $wishlist_id ) ) {
			throw new \InvalidArgumentException( __( 'Invalid request. Please try again.', 'bigcommerce' ), 403 );
		}

		return [
			'id' => $wishlist_id,
		];
	}
}
