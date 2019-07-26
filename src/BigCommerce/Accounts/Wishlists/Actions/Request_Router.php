<?php

namespace BigCommerce\Accounts\Wishlists\Actions;

/**
 * Class Request_Router
 *
 * Routes requests to the wishlist action endpoint to the
 * appropriate action handler
 */
class Request_Router {
	const ACTION = 'wishlist';

	/**
	 * @param $args
	 *
	 * @return void
	 * @action bigcommerce/action_endpoint/ . self::ACTION
	 */
	public function handle_request( array $args ) {
		$action = array_shift( $args );
		do_action( 'bigcommerce/wishlist_endpoint/' . $action, $args );
	}
}
