<?php


namespace BigCommerce\Accounts\Wishlists\Actions;

use BigCommerce\Api\v3\Model\WishlistRequest;
use BigCommerce\Pages\Wishlist_Page;

/**
 * Handles the logic for editing a wishlist.
 *
 * This class processes the request to update the details of an existing wishlist for a customer. 
 * It validates the provided wishlist ID, updates the wishlist's name and public status, and redirects 
 * the user to the updated wishlist. It also triggers success or error actions based on the operation result.
 *
 * @package BigCommerce\Accounts\Wishlists\Actions
 */
class Edit_Wishlist extends Wishlist_Action {
	/**
	 * The action identifier for editing a wishlist.
	 *
	 * This constant is used to identify the specific action for editing a wishlist.
	 *
	 * @var string
	 */
	const ACTION = 'edit';

    /**
     * Handles the request to edit a specific wishlist.
     *
     * This method validates the request, retrieves the wishlist using the provided ID, 
     * updates the wishlist with the new details, and redirects the user to the updated wishlist.
     * It triggers success or failure actions based on the outcome of the operation.
     *
     * @param array $args The arguments from the request.
     * 
     * @return void
     */
	public function handle_request( $args ) {
		$redirect = get_the_permalink( get_option( Wishlist_Page::NAME, 0 ) );
		try {
			$submission       = $this->sanitize_request( $args, $_POST );
			$wishlist         = $this->get_customer_wishlist( get_current_user_id(), $submission['id'] );
			$redirect         = $wishlist->user_url();

			$request = new WishlistRequest( [
				'customer_id' => $wishlist->customer_id(),
				'is_public'   => $submission['public'],
				'name'        => $submission['name'],
				'items'       => [],
			] );
			$this->wishlists->updateWishlist( $submission['id'], $request );

			/**
			 * Trigger success message after updating wishlist.
			 *
			 * @param string $message Success message to display
			 * @param array $submission The submitted form data
			 * @param string $redirect The URL to redirect to
			 * @param array $data Additional data
			 */
			do_action( 'bigcommerce/form/success', __( 'Wish List updated', 'bigcommerce' ), $submission, $redirect, [] );
		} catch ( \Exception $e ) {
			/**
			 * Trigger error message if updating wishlist fails.
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
     * Validates and sanitizes the request to edit a wishlist.
     *
     * This method processes the submission data, ensuring the wishlist ID is valid, sanitizing
     * the request for security, and validating the provided wishlist name. It throws exceptions 
     * for invalid or missing data and returns a sanitized array of the request data.
     *
     * @param array $args The arguments from the request.
     * @param array $submission The submission data from the request.
     * 
     * @return array The sanitized request data, including the wishlist ID, name, and public status.
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
			'name'     => FILTER_SANITIZE_STRING,
			'public'   => FILTER_VALIDATE_BOOLEAN,
		] );

		if ( empty( $submission['_wpnonce'] ) || ! wp_verify_nonce( $submission['_wpnonce'], self::ACTION . $wishlist_id ) ) {
			throw new \InvalidArgumentException( __( 'Invalid request. Please try again.', 'bigcommerce' ), 403 );
		}

		if ( empty( $submission['name'] ) ) {
			throw new \InvalidArgumentException( __( 'Missing Wish List name', 'bigcommerce' ), 400 );
		}

		return [
			'id'     => $wishlist_id,
			'name'   => wp_unslash( sanitize_text_field( $submission['name'] ) ),
			'public' => ! empty( $submission['public'] ),
		];
	}
}
