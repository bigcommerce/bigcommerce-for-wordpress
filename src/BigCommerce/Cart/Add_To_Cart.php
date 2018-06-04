<?php


namespace BigCommerce\Cart;

use BigCommerce\Accounts\Login;
use BigCommerce\Api\v3\ApiException;
use BigCommerce\Api\v3\CartApi;
use BigCommerce\Api\v3\Model\BaseItem;
use BigCommerce\Api\v3\Model\CartRequestData;
use BigCommerce\Api\v3\Model\LineItemRequestData;
use BigCommerce\Post_Types\Product\Product;
use BigCommerce\Settings;
use BigCommerce\Util\Cart_Item_Iterator;

/**
 * Handles requests from the Add to Cart button for products
 */
class Add_To_Cart {
	const ACTION       = 'cart';
	const CART_COOKIE  = 'bigcommerce_cart_id';
	const COUNT_COOKIE = 'bigcommerce_cart_item_count';

	/**
	 * @param int     $post_id
	 * @param CartApi $cart_api
	 *
	 * @return void
	 * @action bigcommerce/action_endpoint/ . self::ACTION
	 */
	public function handle_request( $post_id, CartApi $cart_api ) {
		if ( ! $this->validate_request( $post_id, $_POST ) ) {
			$error = new \WP_Error( 'unknown_product', __( 'There was an error adding this product to your cart. It might be out of stock or unavailable.', 'bigcommerce' ) );
			do_action( 'bigcommerce/form/error', $error, $_POST, home_url( '/' ) );

			return;
		}

		$product = new Product( $post_id );

		$cart_id = $this->get_cart_id();
		if ( $cart_id ) {
			try {
				// make sure the cart is still there
				$cart = $cart_api->cartsCartIdGet( $cart_id );
			} catch ( ApiException $e ) {
				// if the cart is gone, we'll make a new one in a moment
				if ( $e->getCode() == '404' ) {
					$cart_id = '';
				}
			}
		}
		$product_id = $product->bc_id();
		$variant_id = $this->get_variant_id( $product, $_POST );

		$request_data = new CartRequestData();
		$request_data->setLineItems( [
			new LineItemRequestData( [
				'quantity'   => 1,
				'product_id' => $product_id,
				'variant_id' => $variant_id,
			] ),
		] );
		$request_data->setGiftCertificates( [] );
		$cart_url = $this->get_cart_url();
		try {
			if ( $cart_id ) {
				$cart_response = $cart_api->cartsCartIdItemsPost( $cart_id, $request_data );
				if ( $cart_response ) {
					$cart = $cart_response->getData();
				}
			}

			if ( empty( $cart ) ) { // either there was no cart ID passed, or the cart no longer exists
				$customer_id = (int) ( is_user_logged_in() ? get_user_option( Login::CUSTOMER_ID_META, get_current_user_id() ) : 0 );
				if ( $customer_id ) {
					$request_data->setCustomerId( $customer_id );
				}
				$cart    = $cart_api->cartsPost( $request_data )->getData();
				$cart_id = $cart->getId();
				$this->set_cart_id( $cart_id );
			}
			if ( ! empty( $cart ) ) {
				$this->set_item_count_cookie( $cart );
			}
			wp_safe_redirect( esc_url_raw( $cart_url ), 303 );
			exit();
		} catch ( ApiException $e ) {
			$error = new \WP_Error( 'api_error', __( 'There was an error adding this product to your cart. It might be out of stock or unavailable.', 'bigcommerce' ) );
			do_action( 'bigcommerce/form/error', $error, $_POST, $cart_url );
		}
	}

	/**
	 * Get the cart ID from the cookie
	 *
	 * @return string
	 */
	private function get_cart_id() {
		return isset( $_COOKIE[ self::CART_COOKIE ] ) ? $_COOKIE[ self::CART_COOKIE ] : '';
	}

	/**
	 * Set the cookie that contains the cart ID
	 *
	 * @param string $cart_id
	 *
	 * @return void
	 */
	private function set_cart_id( $cart_id ) {
		/**
		 * Filter how long the cart cookie should persist
		 *
		 * @param int $lifetime The cookie lifespan in seconds
		 */
		$cookie_life = apply_filters( 'bigcommerce/cart/cookie_lifetime', 30 * DAY_IN_SECONDS );
		$secure      = ( 'https' === parse_url( home_url(), PHP_URL_SCHEME ) );
		setcookie( self::CART_COOKIE, $cart_id, time() + $cookie_life, COOKIEPATH, COOKIE_DOMAIN, $secure );
	}

	private function validate_request( $post_id, $submission ) {
		$post = get_post( $post_id );
		if ( empty( $post ) ) {
			return false;
		}
		if ( Product::NAME !== $post->post_type ) {
			return false;
		}
		if ( $post->post_status !== 'publish' ) {
			return false;
		}

		return true;
	}

	private function get_variant_id( Product $product, $submission ) {
		if ( ! empty( $submission[ 'variant_id' ] ) ) {
			return $submission[ 'variant_id' ];
		}

		$data = $product->get_source_data();

		foreach ( $data->variants as $variant ) {
			foreach ( $variant->option_values as $option ) {
				$key = 'option-' . $option->option_id;
				if ( ! isset( $submission[ $key ] ) ) {
					continue 2;
				}
				if ( $submission[ $key ] != $option->id ) {
					continue 2;
				}
			}

			// all options matched, we have a winner
			return $variant->id;
		}

		// fall back to the first variant
		if ( ! empty( $data->variants ) ) {
			return reset( $data->variants )->id;
		}

		return 0;
	}

	private function get_cart_url() {
		$cart_page_id = get_option( Settings\Cart::OPTION_CART_PAGE_ID, 0 );
		if ( empty( $cart_page_id ) ) {
			$url = home_url( '/' );
		} else {
			$url = get_permalink( $cart_page_id );
		}

		/**
		 * Filter the URL to the cart page
		 *
		 * @param string $url     The URL to the cart page
		 * @param int    $page_id The ID of the cart page
		 */
		return apply_filters( 'bigcommerce/cart/permalink', $url, $cart_page_id );
	}

	/**
	 * Set a temporary cookie with the count of items
	 * in the cart. Front end will use it for updating
	 * the cart menu item.
	 *
	 * @param \BigCommerce\Api\v3\Model\Cart $cart
	 *
	 * @return void
	 */
	private function set_item_count_cookie( \BigCommerce\Api\v3\Model\Cart $cart ) {
		$count  = array_reduce(
			iterator_to_array( Cart_Item_Iterator::factory( $cart ) ),
			function ( $count, BaseItem $item ) {
				$count += $item->getQuantity();

				return $count;
			},
			0
		);
		$secure = ( 'https' === parse_url( home_url(), PHP_URL_SCHEME ) );
		setcookie( self::COUNT_COOKIE, $count, time() + MINUTE_IN_SECONDS, COOKIEPATH, COOKIE_DOMAIN, $secure );
	}
}