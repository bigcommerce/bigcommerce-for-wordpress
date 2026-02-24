<?php


namespace BigCommerce\Accounts\Wishlists\Actions;

use BigCommerce\Api\v3\Model\WishlistAddItemsRequest;
use BigCommerce\Api\v3\Model\WishlistItem;
use BigCommerce\Exceptions\Product_Not_Found_Exception;
use BigCommerce\Pages\Wishlist_Page;
use BigCommerce\Post_Types\Product\Product;

/**
 * Handles requests for adding items to a customer's wishlist.
 * 
 * This class processes a request to add a product to the wishlist, sanitizes the request data,
 * verifies its validity, adds the item, and then provides a success or error message accordingly.
 *
 * @package BigCommerce\Accounts\Wishlists\Actions
 */
class Add_Item extends Wishlist_Action {

	/**
	 * The action identifier for adding an item.
	 *
	 * This constant is used to identify the specific action for adding an item to the wishlist.
	 *
	 * @var string
	 */
	const ACTION = 'add-item';

	/**
	 * Handles the request to add an item to the wishlist.
	 *
	 * This method sanitizes the incoming request, verifies the wishlist and product IDs, 
	 * and adds the item to the wishlist. If successful, a success message is triggered; 
	 * if an error occurs, an error message is returned.
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

			$this->wishlists->addWishlistItems( $submission['wishlist_id'], new WishlistAddItemsRequest( [
				'items' => [
					new WishlistItem( [
						'product_id' => $product_id,
					] ),
				],
			] ) );

			try {
				// Try to link to the product single in the message
				$product = Product::by_product_id( $product_id );
				$url     = get_the_permalink( $product->post_id() );
				$title   = sprintf( '<a href="%s">%s</a>', esc_url( $url ), wp_strip_all_tags( get_the_title( $product->post_id() ) ) );
				$message = sprintf( __( '"%s" added to Wish List', 'bigcommerce' ), $title );
			} catch ( Product_Not_Found_Exception $e ) {
				$message = __( 'Item added to Wish List', 'bigcommerce' );
			}

			/**
			 * Trigger success message after adding item to wishlist.
			 *
			 * @param string $message Success message to display
			 * @param array $submission The submitted form data
			 * @param string $redirect The URL to redirect to
			 * @param array $data Additional data
			 */
			do_action( 'bigcommerce/form/success', $message, $submission, $redirect, [] );
		} catch ( \Exception $e ) {
			/**
			 * Trigger error message if adding item to wishlist fails.
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
	 * Cleans up and sanitizes the add item request.
	 *
	 * This method verifies the wishlist ID and product ID, checks the nonce for security,
	 * and ensures all required fields are provided and valid.
	 *
	 * @param array $args The arguments from the request.
	 * @param array $submission The submission data from the request.
	 * 
	 * @return array The sanitized request data, including the wishlist and product IDs.
	 * 
	 * @throws \InvalidArgumentException If the request is invalid or missing required data.
	 */
	protected function sanitize_request( array $args, array $submission ) {
		$wishlist_id = reset( $args );
		if ( empty( $wishlist_id ) || ! is_numeric( $wishlist_id ) ) {
			throw new \InvalidArgumentException( __( 'Invalid Wish List ID', 'bigcommerce' ), 400 );
		}

		$submission = filter_var_array( $submission, [
			'_wpnonce'    => FILTER_SANITIZE_STRING,
			'product_id'  => FILTER_VALIDATE_INT,
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
