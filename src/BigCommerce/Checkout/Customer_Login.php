<?php


namespace BigCommerce\Checkout;


use BigCommerce\Accounts\Login;
use BigCommerce\Api\Store_Api;
use BigCommerce\Exceptions\Channel_Not_Found_Exception;
use BigCommerce\Merchant\Models\Customer_Login_Request;
use BigCommerce\Merchant\Onboarding_Api;
use BigCommerce\Settings\Sections\Api_Credentials;
use BigCommerce\Settings\Sections\Channels;
use BigCommerce\Taxonomies\Channel\Channel;
use BigCommerce\Taxonomies\Channel\Connections;

/**
 * Handles customer login logic during the checkout process.
 *
 * @package BigCommerce\Checkout
 */
class Customer_Login {
	/**
	 * @var Onboarding_Api API for merchant onboarding processes.
	 */
	private $onboarding;

	/**
	 * @var Store_Api API for interacting with the store.
	 */
	private $store;

	/**
	 * Constructor for the `Customer_Login` class.
	 *
	 * @param Onboarding_Api $onboarding API instance for onboarding.
	 * @param Store_Api      $store      API instance for store interactions.
	 */
	public function __construct( Onboarding_Api $onboarding, Store_Api $store ) {
		$this->onboarding = $onboarding;
		$this->store = $store;
	}

	/**
	 * Generate a new login token for the customer and update the checkout URL.
	 *
	 * Depending on whether the store is created through the onboarding process
	 * or uses custom API credentials, it generates a login token for the customer.
	 *
	 * @param string $checkout_url The original checkout URL.
	 *
	 * @return string The updated checkout URL with the login token appended.
	 *
	 * @filter bigcommerce/checkout/url
	 */
	public function set_login_token_for_checkout( $checkout_url ) {
		$customer_id = (int) ( is_user_logged_in() ? get_user_option( Login::CUSTOMER_ID_META, get_current_user_id() ) : 0 );
		$hash = $this->get_store_hash();
		$store_id = get_option( Onboarding_Api::STORE_ID, '' );
		$channel_id = $this->get_channel_id();

		if ( ! $customer_id || ! $hash ) {
			return $checkout_url;
		}

		try {
			if ( $store_id ) {
				// for accounts created through the onboarding process
				$request = new Customer_Login_Request( $hash, $checkout_url, $channel_id );
				$token = $this->onboarding->customer_login_token( $store_id, $customer_id, $request );
			} else {
				// for customers that set their own API keys in constants
				$token = $this->store->getCustomerLoginToken( $customer_id, $checkout_url, '', $channel_id );
			}
		} catch ( \Exception $e ) {
			return $checkout_url;
		}

		try {
			$store        = $this->store->getStore();
			$checkout_url = $store->secure_url . '/login/token/' . $token;
		} catch ( \Exception $e ) {
			return $checkout_url;
		}

		return $checkout_url;
	}

	/**
	 * Get the store hash from the API URL
	 *
	 * @return string
	 */
	private function get_store_hash() {
		$url = get_option( Api_Credentials::OPTION_STORE_URL, '' );
		if ( empty( $url ) ) {
			return '';
		}
		preg_match( '#stores/([^\/]+)/#', $url, $matches );
		if ( empty( $matches[ 1 ] ) ) {
			return '';
		} else {
			return $matches[ 1 ];
		}
	}

    /**
     * Get current channel id
     *
     * @return int
     */
	private function get_channel_id() {
		try {
			$connections = new Connections();
			$current = $connections->current();
			return (int) get_term_meta( $current->term_id, Channel::CHANNEL_ID, true );
		} catch ( Channel_Not_Found_Exception $e ) {
			return 0;
		}
	}
}
