<?php


namespace BigCommerce\Accounts\Wishlists\Actions;

use BigCommerce\Pages\Wishlist_Page;

/**
 * Class Delete_Wishlist
 *
 * Handle delete wishlist logic
 */
class Delete_Wishlist extends Wishlist_Action {
	const ACTION = 'delete';

    /**
     * Delete requested wishlist
     *
     * @param $args
     */
	public function handle_request( $args ) {
		$redirect = get_the_permalink( get_option( Wishlist_Page::NAME, 0 ) );
		try {
			$submission = $this->sanitize_request( $args, $_POST );
			$wishlist   = $this->get_customer_wishlist( get_current_user_id(), $submission['id'] );
			$this->wishlists->deleteWishlist( $wishlist->list_id() );

			do_action( 'bigcommerce/form/success', __( 'Wish List deleted', 'bigcommerce' ), $submission, $redirect, [] );
		} catch ( \Exception $e ) {
			do_action( 'bigcommerce/form/error', new \WP_Error( $e->getCode(), $e->getMessage() ), $_POST, $redirect, [] );
		}
	}

    /**
     * Validate and sanitize request
     *
     * @param array $args
     * @param array $submission
     *
     * @return int[]|string[]
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
