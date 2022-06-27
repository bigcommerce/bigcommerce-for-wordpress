<?php


namespace BigCommerce\Cart;


use BigCommerce\Accounts\Login;
use BigCommerce\Api\v3\ApiException;
use BigCommerce\Api\v3\Api\CartApi;
use BigCommerce\Api\v3\Model\CartRequestData;
use BigCommerce\Api\v3\Model\Currency;
use BigCommerce\Api\v3\Model\LineItemGiftCertificateRequestData;
use BigCommerce\Api\v3\Model\LineItemRequestData;
use BigCommerce\Api\v3\Model\ProductOptionSelection;
use BigCommerce\Exceptions\Channel_Not_Found_Exception;
use BigCommerce\Settings;
use BigCommerce\Taxonomies\Channel\Channel;
use BigCommerce\Taxonomies\Channel\Connections;
use BigCommerce\Util\Cart_Item_Iterator;

/**
 * Class Cart
 *
 * @package BigCommerce\Cart
 */
class Cart {
	const CART_COOKIE  = 'wp-bigcommerce_cart_id';
	const COUNT_COOKIE = 'wp-bigcommerce_cart_item_count';
	/**
	 * @var CartApi
	 */
	private $api;

	public function __construct( CartApi $api ) {
		$this->api = $api;
	}

	/**
	 * Get the cart ID from the cookie
	 *
	 * @return string
	 */
	public function get_cart_id() {
		$cart_id = '';
		$cookie  = filter_input( INPUT_COOKIE, self::CART_COOKIE, FILTER_SANITIZE_STRING );
		if ( $cookie && get_option( Settings\Sections\Cart::OPTION_ENABLE_CART, true ) ) {
			$cart_id = $cookie;
		}

		/**
		 * Filter the cart ID to use for the current request
		 *
		 * @param string $cart_id
		 */
		return apply_filters( 'bigcommerce/cart/cart_id', $cart_id );
	}

	/**
	 * Set the cookie that contains the cart ID
	 *
	 * @param string $cart_id
	 *
	 * @return void
	 */
	public function set_cart_id( $cart_id ) {
		/**
		 * Filter how long the cart cookie should persist
		 *
		 * @param int $lifetime The cookie lifespan in seconds
		 */
		$cookie_life = apply_filters( 'bigcommerce/cart/cookie_lifetime', 30 * DAY_IN_SECONDS );
		$secure      = ( 'https' === parse_url( home_url(), PHP_URL_SCHEME ) );
		setcookie( self::CART_COOKIE, $cart_id, time() + $cookie_life, COOKIEPATH, COOKIE_DOMAIN, $secure );
		$_COOKIE[ self::CART_COOKIE ] = $cart_id;
	}

	/**
	 * @param int   $product_id The BigCommerce ID of the product
	 * @param array $options    All options and modifiers for the line item
	 * @param int   $quantity   How many to add to the cart
	 * @param array $modifiers  Deprecated in 1.7.0, all values should be passed in $options
	 *
	 * @return \BigCommerce\Api\v3\Model\Cart
	 * @throws ApiException
	 */
	public function add_line_item( $product_id, $options = [], $quantity = 1, $modifiers = [] ) {
		$request_data      = new CartRequestData();
		$option_selections = [];

		foreach ( $options as $option_key => $option_value ) {
			if ( $option_value !== 0 ) {
				$option_selections[] = new ProductOptionSelection( [
					'option_id'    => $option_key,
					'option_value' => $option_value,
				] );
			}
		}

		/*
		 * Kept for backwards compatibility. Modifiers are treated
		 * the same as options when submitting a line item.
		 */
		foreach ( $modifiers as $modifier_key => $modifier_value ) {
			$option_selections[] = new ProductOptionSelection( [
				'option_id'    => $modifier_key,
				'option_value' => $modifier_value,
			] );
		}

		$request_data->setLineItems( [
			new LineItemRequestData( [
				'quantity'          => $quantity,
				'product_id'        => $product_id,
				'option_selections' => $option_selections,
			] ),
		] );
		$request_data->setGiftCertificates( [] );

		return $this->add_request_to_cart( $request_data );
	}

	/**
	 * @param $certificate
	 *
	 * @return \BigCommerce\Api\v3\Model\Cart
	 * @throws ApiException
	 */
	public function add_gift_certificate( $certificate ) {
		$request_data = new CartRequestData();
		$request_data->setLineItems( [] );
		$request_data->setGiftCertificates( [
			new LineItemGiftCertificateRequestData( $certificate ),
		] );

		return $this->add_request_to_cart( $request_data );
	}

	/**
	 * @param CartRequestData $request
	 *
	 * @return \BigCommerce\Api\v3\Model\Cart
	 *
	 * @throws ApiException
	 */
	private function add_request_to_cart( CartRequestData $request ) {
		$cart    = null;
		$cart_id = $this->get_cart_id();

		// validates that the cart still exists
		$cart_id = $this->sanitize_cart_id( $cart_id );

		// Add to the existing cart
		if ( $cart_id ) {
			/* @throws ApiException if the request failes */
			$cart_response = $this->api->cartsCartIdItemsPost( $cart_id, $request );
			$cart = $cart_response->getData();
		} else { // either there was no cart ID passed, or the cart no longer exists, so build a new cart
			$customer_id = (int) ( is_user_logged_in() ? get_user_option( Login::CUSTOMER_ID_META, get_current_user_id() ) : 0 );
			if ( $customer_id ) {
				$request->setCustomerId( $customer_id );
			}
			$channel_id = $this->get_channel_id();
			if ( $channel_id ) {
				$request->setChannelId( $channel_id );
			}
			$currency = $this->get_currency();
			if ( $currency ) {
				$request->setCurrency( new Currency( [ 'code' => $currency ] ) );
			}
			try {
				$cart    = $this->api->cartsPost( $request )->getData();
				$cart_id = $cart->getId();
				$this->set_cart_id( $cart_id );
			} catch ( ApiException $e ) {
				// request failed. cannot create a new cart
				$this->set_cart_id( '' );
				throw $e; // pass it up the call stack
			}
		}

		$this->set_item_count_cookie( $cart );

		// return a fully-populated cart object, which we can't get with the post requests
		return $this->api->cartsCartIdGet( $cart_id, [
			'line_items.physical_items.options',
			'line_items.digital_items.options',
			'redirect_urls',
		] )->getData();
	}

	public function sanitize_cart_id( $cart_id ) {
		if ( $cart_id ) {
			try {
				// make sure the cart is still there
				$cart = $this->api->cartsCartIdGet( $cart_id );
			} catch ( ApiException $e ) {
				if ( $e->getCode() == '404' ) {
					$cart_id = '';
				}
			}
		}

		return $cart_id;
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
		$count = Item_Counter::count_bigcommerce_cart( $cart );
		$secure = ( 'https' === parse_url( home_url(), PHP_URL_SCHEME ) );
		setcookie( self::COUNT_COOKIE, $count, time() + MINUTE_IN_SECONDS, COOKIEPATH, COOKIE_DOMAIN, $secure );
		$_COOKIE[ self::COUNT_COOKIE ] = $count;
	}

	public function get_cart_url() {
		$cart_page_id = get_option( Settings\Sections\Cart::OPTION_CART_PAGE_ID, 0 );
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
	 * @param string $cart_id The ID of the user's cart. Defaults to the ID found in the cart cookie
	 *
	 * @return string The URL for checking out with the given cart
	 */
	public function get_checkout_url( $cart_id ) {
		$cart_id = $cart_id ?: $this->get_cart_id();
		if ( empty( $cart_id ) ) {
			return '';
		}
		try {
			$redirects = $this->api->cartsCartIdRedirectUrlsPost( $cart_id )->getData();
		} catch ( ApiException $e ) {
			return '';
		}
		$checkout_url = $redirects['checkout_url'];

		/**
		 * Filters checkout url.
		 *
		 * @param string $checkout_url The URL for checking out with the given cart.
		 */
		$checkout_url = apply_filters( 'bigcommerce/checkout/url', $checkout_url );

		return $checkout_url;
	}

	public function get_embedded_checkout_url( $cart_id ) {
		$cart_id = $cart_id ?: $this->get_cart_id();
		if ( empty( $cart_id ) ) {
			return '';
		}
		try {
			$redirects = $this->api->cartsCartIdRedirectUrlsPost( $cart_id )->getData();
		} catch ( ApiException $e ) {
			return '';
		}
		$checkout_url = $redirects['embedded_checkout_url'];

		/**
		 * Filters checkout url.
		 *
		 * @param string $checkout_url The URL for checking out with the given cart.
		 */
		$checkout_url = apply_filters( 'bigcommerce/checkout/url', $checkout_url );

		return $checkout_url;
	}

	private function get_channel_id() {
		try {
			$connections = new Connections();
			$current     = $connections->current();

			return (int) get_term_meta( $current->term_id, Channel::CHANNEL_ID, true );
		} catch ( Channel_Not_Found_Exception $e ) {
			return 0;
		}
	}

	private function get_currency() {
		return get_option( Settings\Sections\Currency::CURRENCY_CODE, 'USD' );
	}
}
