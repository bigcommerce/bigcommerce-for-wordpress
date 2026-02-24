<?php


namespace BigCommerce\Accounts\Wishlists\Actions;

use BigCommerce\Api\v3\Model\WishlistItem;
use BigCommerce\Exceptions\Product_Not_Found_Exception;
use BigCommerce\Pages\Wishlist_Page;
use BigCommerce\Post_Types\Product\Product;

/**
 * Handles the logic for removing an item from a customer's wishlist.
 *
 * This class processes the request to remove a specific product from a wishlist. 
 * It ensures that the provided wishlist and product IDs are valid, removes the item from the wishlist, 
 * and returns a success or error message accordingly.
 *
 * @package BigCommerce\Accounts\Wishlists\Actions
 */
class Remove_Item extends Wishlist_Action {
	/**
	 * The action identifier for removing an item from a wishlist.
	 *
	 * This constant is used to identify the specific action for removing an item from the wishlist.
	 *
	 * @var string
	 */
	const ACTION = 'remove-item';

    /**
     * Handles the request to remove a product from a specific wishlist.
     *
     * This method validates the request, retrieves the wishlist and product details, 
     * and removes the product from the wishlist if it exists. A success or failure message is returned 
     * based on the result of the operation.
     *
     * @param array $args The arguments from the request.
     * 
     * @return void
     */
	public function handle_request( $args ) {
		$redirect = get_the_permalink( get_option( Wishlist_Page::NAME, 0 ) );
		try {
			$submission = $this->sanitize_request( $args, $_REQUEST );
			$wishlist   = $this->get_customer_wishlist( get_current_user_id(), $submission['wishlist_id'] );
			$redirect   = $wishlist->user_url();
			$product_id = (int) $submission['product_id'];

			/** @var WishlistItem[] $items */
			$items = array_filter( $wishlist->raw_items(), function ( WishlistItem $item ) use ( $product_id ) {
				return (int) $item->getProductId() === $product_id;
			} );

			/*
			 * Should only be one item, but just in case, we'll loop through anything that matched the filter
			 */
			foreach ( $items as $item ) {
				$this->wishlists->deleteWishlistItem( $submission['wishlist_id'], $item->getId() );
			}

			try {
				// Try to link to the product single in the message
				$product = Product::by_product_id( $product_id );
				$url     = get_the_permalink( $product->post_id() );
				$title   = sprintf( '<a href="%s">%s</a>', esc_url( $url ), wp_strip_all_tags( get_the_title( $product->post_id() ) ) );
				$message = sprintf( __( '"%s" removed from Wish List', 'bigcommerce' ), $title );
			} catch ( Product_Not_Found_Exception $e ) {
				$message = __( 'Item removed from Wish List', 'bigcommerce' );
			}

			/**
			 * Trigger success message after removing item from wishlist.
			 *
			 * @param string $message Success message to display
			 * @param array $submission The submitted form data
			 * @param string $redirect The URL to redirect to
			 * @param array $data Additional data
			 */
			do_action( 'bigcommerce/form/success', $message, $submission, $redirect, [] );
		} catch ( \Exception $e ) {
			/**
			 * Trigger error message if removing item from wishlist fails.
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
     * Sanitizes and validates the request to remove an item from a wishlist.
     *
     * This method ensures the wishlist ID and product ID are valid, sanitizes the request data, 
     * and verifies the nonce to prevent unauthorized requests. It returns the validated data or throws 
     * an exception if the data is invalid.
     *
     * @param array $args The arguments from the request.
     * @param array $submission The request data to be validated.
     * 
     * @return array The sanitized and validated request data.
     * 
     * @throws \InvalidArgumentException If the wishlist ID or product ID is invalid, or if the nonce is incorrect.
     */
	protected function sanitize_request( array $args, array $submission ) {
		$wishlist_id = reset( $args );
		if ( empty( $wishlist_id ) || ! is_numeric( $wishlist_id ) ) {
			throw new \InvalidArgumentException( __( 'Invalid Wish List ID', 'bigcommerce' ), 400 );
		}

		$submission = filter_var_array( $submission, [
			'_wpnonce'   => FILTER_SANITIZE_STRING,
			'product_id' => FILTER_VALIDATE_INT,
		] );

		if ( empty( $submission['_wpnonce'] ) || ! wp_verify_nonce( $submission['_wpnonce'], sprintf( '%s/%d/%d', self::ACTION, $wishlist_id, $submission['product_id'] ) ) ) {
			throw new \InvalidArgumentException( __( 'Invalid request. Please try again.', 'bigcommerce' ), 403 );
		}

		if ( empty( $submission['product_id'] ) ) {
			throw new \InvalidArgumentException( __( 'Missing product ID.', 'bigcommerce' ), 400 );
		}

		return [
			'wishlist_id' => $wishlist_id,
			'product_id'  => $submission['product_id'],
		];
	}
}
