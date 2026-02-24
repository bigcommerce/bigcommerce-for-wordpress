<?php


namespace BigCommerce\Cart;

use BigCommerce\Api\v3\Api\AbandonedCartApi;
use BigCommerce\Api\v3\Api\CartApi;
use BigCommerce\Api\v3\ApiException;
use BigCommerce\Post_Types\Product\Product;
use BigCommerce\Settings\Sections\Cart as CartSettings;

/**
 * The Cart_Recovery class is responsible for handling the recovery of abandoned carts.
 * It interacts with BigCommerce's API to retrieve and recover abandoned carts based on 
 * a unique token provided in the request. It also manages the redirection process and 
 * the setting of cookies for recovered carts, ensuring that the user is taken to the correct 
 * cart page and that the abandoned cart is appropriately handled.
 *
 * @package BigCommerce\Cart
 */
class Cart_Recovery {
	/**
	 * The action hook name for cart recovery.
	 *
	 * @var string
	 */
	const ACTION = 'recover-cart';

	/**
	 * @var CartApi
	 */
	private $cart_api;

	/**
	 * @var AbandonedCartApi
	 */
	private $api;

	/**
	 * Cart_Recovery constructor.
	 *
	 * Initializes the Cart_Recovery class with the given AbandonedCartApi and CartApi instances.
	 *
	 * @param AbandonedCartApi $api An instance of the AbandonedCartApi to interact with the BigCommerce API for cart recovery.
	 * @param CartApi          $cart An instance of the CartApi to interact with the BigCommerce API for cart-related actions.
	 */
	public function __construct( AbandonedCartApi $api , CartApi $cart) {
		$this->api      = $api;
		$this->cart_api = $cart;
	}

	/**
	 * Handles the incoming request to recover an abandoned cart.
	 *
	 * This function retrieves the token from the query parameters, validates it, 
	 * and then attempts to recover the abandoned cart using the BigCommerce API.
	 * If the cart is recovered successfully, it sets a cookie with the abandoned 
	 * cart's ID and redirects the user to the cart page. If there is an error, 
	 * it handles the error and redirects to the appropriate page.
	 *
	 * @return string The URL of the cart page, either with the recovered cart or as a fallback.
	 */
	public function handle_request(  ) {
		$token = filter_input( INPUT_GET, 't', FILTER_SANITIZE_STRING );

		if ( empty( $token ) ) {
			wp_die( esc_html( __( 'Bad Request', 'bigcommerce' ) ), esc_html( __( 'Bad Request', 'bigcommerce' ) ), 400 );
			exit();
		}

		$cart_page_id = 0;
		if ( get_option( CartSettings::OPTION_ENABLE_CART, true ) ) {
			$cart_page_id = get_option( \BigCommerce\Pages\Cart_Page::NAME, 0 );
		}

		try {
			$abandoned_cart = $this->api->recoverCart( $token )->getData()->getCartId();
		} catch ( ApiException $e ) {
			$error = new \WP_Error( 'api_error', $e->getMessage() );

			if ( $cart_page_id ) {
				$destination = get_permalink( $cart_page_id );
			} else {
				$destination = get_post_type_archive_link( Product::NAME );
			}
			/**
			 * Fires when an error occurs during cart recovery.
			 *
			 * @param \WP_Error $error      The WordPress error object containing the error details
			 * @param array     $_POST      The POST data from the request
			 * @param string    $destination The URL where the user will be redirected after the error
			 */
			do_action( 'bigcommerce/form/error', $error, $_POST, $destination );
		}

		if ( empty( $abandoned_cart ) )  {
			$this->redirect_to_cart( $cart_page_id );
		} else {
			$this->set_abandoned_cart_cookie( $abandoned_cart );
		}
		$this->redirect_to_cart( $cart_page_id );
	}

	/**
	 * Redirects the user to the cart page.
	 *
	 * This function redirects the user to the specified cart page using the cart page ID.
	 * It performs the redirection and stops the script execution.
	 *
	 * @param int $cart_page_id The ID of the cart page to redirect to.
	 */
	private function redirect_to_cart( $cart_page_id ){
		wp_redirect( get_permalink ( $cart_page_id ) );
		die();
	}

	/**
	 * Sets a cookie for the recovered abandoned cart.
	 *
	 * This function creates a Cart object and sets the cart ID of the recovered abandoned cart
	 * to associate the user's session with the cart they are recovering.
	 *
	 * @param int $abandoned_cart The ID of the abandoned cart to be recovered.
	 */
	private function set_abandoned_cart_cookie( $abandoned_cart ){
		$cart = new \BigCommerce\Cart\Cart( $this->cart_api );
		$cart->set_cart_id( $abandoned_cart );
	}

}
