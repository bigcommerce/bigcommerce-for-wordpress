<?php


namespace BigCommerce\Accounts\Wishlists\Actions;

use BigCommerce\Api\v3\Model\WishlistRequest;
use BigCommerce\Pages\Wishlist_Page;

class Edit_Wishlist extends Wishlist_Action {
	const ACTION = 'edit';

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

			do_action( 'bigcommerce/form/success', __( 'Wish List updated', 'bigcommerce' ), $submission, $redirect, [] );
		} catch ( \Exception $e ) {
			do_action( 'bigcommerce/form/error', new \WP_Error( $e->getCode(), $e->getMessage() ), $_POST, $redirect, [] );
		}
	}

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
