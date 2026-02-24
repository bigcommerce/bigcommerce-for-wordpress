<?php

namespace BigCommerce\Accounts\Wishlists\Actions;

/**
 * Class Request_Router
 *
 * Routes incoming requests to the appropriate action handler for wishlist-related operations.
 *
 * This class listens for requests on the wishlist action endpoint and directs them to the appropriate 
 * handler based on the action specified in the request.
 *
 * @package BigCommerce\Accounts\Wishlists\Actions
 */
class Request_Router {
	/**
	 * The action identifier for the wishlist endpoint.
	 *
	 * This constant is used to identify the wishlist action endpoint in the routing process.
	 *
	 * @var string
	 */
	const ACTION = 'wishlist';

    /**
     * Handles the incoming request by routing it to the appropriate action handler.
     *
     * This method extracts the action from the request arguments, and then triggers a WordPress action 
     * to handle the request using the specific action handler associated with the action.
     * 
     * @param array $args The arguments for the wishlist request, including the action and any parameters.
     *
     * @return void
     *
     * @action bigcommerce/action_endpoint/ . self::ACTION
     */
	public function handle_request( array $args ) {
		$action = array_shift( $args );
		/**
		 * Routes the request to the appropriate wishlist action handler.
		 *
		 * @param string $action The wishlist action to be executed
		 * @param array $args The arguments for the wishlist request
		 */
		do_action( 'bigcommerce/wishlist_endpoint/' . $action, $args );
	}
}
