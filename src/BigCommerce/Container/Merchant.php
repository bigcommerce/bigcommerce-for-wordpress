<?php


namespace BigCommerce\Container;


use BigCommerce\Merchant\Account_Status;
use BigCommerce\Merchant\Connect_Account;
use BigCommerce\Merchant\Create_Account;
use BigCommerce\Merchant\Onboarding_Api;
use BigCommerce\Merchant\Setup_Status;
use BigCommerce\Settings\Screens\Pending_Account_Screen;
use Pimple\Container;

class Merchant extends Provider {
	const MIDDLEMAN_URL   = 'merchant.middleman.url';
	const ONBOARDING_API  = 'merchant.onboarding.api';
	const CREATE_ACCOUNT  = 'merchant.onboarding.create_account';
	const CONNECT_ACCOUNT = 'merchant.onboarding.connect_account';
	const ACCOUNT_STATUS  = 'merchant.onboarding.account_status';
	const SETUP_STATUS    = 'merchant.onboarding.setup_status';

	public function register( Container $container ) {
		$this->account_onboarding( $container );
	}

	private function account_onboarding( Container $container ) {
		$container[ self::MIDDLEMAN_URL ] = function ( Container $container ) {
			return apply_filters( 'bigcommerce/oauth_connector/url', 'https://wp-login.bigcommerce.com/v1' );
		};

		$container[ self::ONBOARDING_API ] = function ( Container $container ) {
			return new Onboarding_Api( $container[ self::MIDDLEMAN_URL ] );
		};

		$this->create_account( $container );
		$this->connect_account( $container );
		$this->account_status( $container );
		$this->setup_status( $container );
	}

	private function create_account( Container $container ) {
		$container[ self::CREATE_ACCOUNT ] = function ( Container $container ) {
			return new Create_Account( $container[ self::ONBOARDING_API ] );
		};

		add_action( 'bigcommerce/create_account/submit_request', $this->create_callback( 'request_account', function ( $data, $errors ) use ( $container ) {
			$container[ self::CREATE_ACCOUNT ]->request_account( $data, $errors );
		} ), 10, 2 );
	}

	private function connect_account( Container $container ) {
		$container[ self::CONNECT_ACCOUNT ] = function ( Container $container ) {
			return new Connect_Account( $container[ self::ONBOARDING_API ] );
		};

		add_filter( 'bigcommerce/settings/connect_account_url', $this->create_callback( 'connect_account_url', function ( $url ) use ( $container ) {
			return $container[ self::CONNECT_ACCOUNT ]->connect_account_url( $url );
		} ), 10, 1 );

		add_action( 'admin_post_' . Connect_Account::CONNECT_ACTION, $this->create_callback( 'connect_account_handler', function () use ( $container ) {
			$container[ self::CONNECT_ACCOUNT ]->connect_account();
		} ), 10, 0 );
	}

	private function account_status( Container $container ) {
		$container[ self::ACCOUNT_STATUS ] = function ( Container $container ) {
			return new Account_Status( $container[ self::ONBOARDING_API ] );
		};

		add_action( 'bigcommerce/settings/after_content/page=' . Pending_Account_Screen::NAME, $this->create_callback( 'account_status_placeholder', function () use ( $container ) {
			$container[ self::ACCOUNT_STATUS ]->render_status_placeholder();
		} ), 10, 0 );
		add_action( 'wp_ajax_' . Account_Status::STATUS_AJAX, $this->create_callback( 'ajax_account_status', function () use ( $container ) {
			$container[ self::ACCOUNT_STATUS ]->handle_account_status_request();
		} ), 10, 0 );
		add_action( 'bigcommerce/pending_account/check_status', $this->create_callback( 'pending_check_status', function ( $errors ) use ( $container ) {
			$container[ self::ACCOUNT_STATUS ]->handle_refresh_status_request( $errors );
		} ), 10, 1 );
	}

	private function setup_status( Container $container ) {
		$container[ self::SETUP_STATUS ] = function ( Container $container ) {
			return new Setup_Status( $container[ Api::FACTORY ] );
		};
	}
}
