<?php


namespace BigCommerce\Cart;

use BigCommerce\Api\v3\Api\AbandonedCartApi;
use BigCommerce\Api\v3\Api\CartApi;
use BigCommerce\Api\v3\ApiException;
use BigCommerce\Post_Types\Product\Product;
use BigCommerce\Settings\Sections\Cart as CartSettings;

class Cart_Recovery {
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
	 * @param AbandonedCartApi $api
	 * @param CartApi          $cart
	 */
	public function __construct( AbandonedCartApi $api , CartApi $cart) {
		$this->api      = $api;
		$this->cart_api = $cart;
	}

	/**
	 *
	 * @return string
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
	 * @param int $cart_page_id
	 */
	private function redirect_to_cart( $cart_page_id ){
		wp_redirect( get_permalink ( $cart_page_id ) );
		die();
	}

	/**
	 * @param int $abandoned_cart
	 */
	private function set_abandoned_cart_cookie( $abandoned_cart ){
		$cart = new \BigCommerce\Cart\Cart( $this->cart_api );
		$cart->set_cart_id( $abandoned_cart );
	}

}
