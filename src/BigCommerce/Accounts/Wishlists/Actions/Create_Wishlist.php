<?php


namespace BigCommerce\Accounts\Wishlists\Actions;

use BigCommerce\Accounts\Wishlists\Wishlist;
use BigCommerce\Api\v3\Model\WishlistItem;
use BigCommerce\Api\v3\Model\WishlistRequest;
use BigCommerce\Pages\Wishlist_Page;

class Create_Wishlist extends Wishlist_Action {
	const ACTION = 'create';

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

			do_action( 'bigcommerce/form/success', __( 'Wish List created', 'bigcommerce' ), $submission, $redirect, [] );
		} catch ( \Exception $e ) {
			do_action( 'bigcommerce/form/error', new \WP_Error( $e->getCode(), $e->getMessage() ), $_POST, $redirect, [] );
		}
	}

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
