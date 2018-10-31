<?php


namespace BigCommerce\Checkout;


use BigCommerce\Accounts\Login;
use BigCommerce\Api\Store_Api;
use BigCommerce\Merchant\Models\Customer_Login_Request;
use BigCommerce\Merchant\Onboarding_Api;
use BigCommerce\Settings\Sections\Api_Credentials;

class Customer_Login {
	/**
	 * @var Onboarding_Api
	 */
	private $onboarding;

	/**
	 * @var Store_Api
	 */
	private $store;

	public function __construct( Onboarding_Api $onboarding, Store_Api $store ) {
		$this->onboarding = $onboarding;
		$this->store = $store;
	}


	/**
	 * @param string $checkout_url
	 *
	 * @return string
	 * @filter bigcommerce/checkout/url
	 */
	public function set_login_token_for_checkout( $checkout_url ) {
		$customer_id = (int) ( is_user_logged_in() ? get_user_option( Login::CUSTOMER_ID_META, get_current_user_id() ) : 0 );
		$hash = $this->get_store_hash();
		$store_id = get_option( Onboarding_Api::STORE_ID, '' );

		if ( ! $customer_id || ! $hash ) {
			return $checkout_url;
		}

		try {
			if ( $store_id ) {
				// for accounts created through the onboarding process
				$request = new Customer_Login_Request( $hash, $checkout_url );
				$token = $this->onboarding->customer_login_token( $store_id, $customer_id, $request );
			} else {
				// for customers that set their own API keys in constants
				$token = $this->store->getCustomerLoginToken( $customer_id, $checkout_url );
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
}