<?php


namespace BigCommerce\Container;


use BigCommerce\Merchant\Account_Status;
use BigCommerce\Merchant\Connect_Account;
use BigCommerce\Merchant\Create_Account;
use BigCommerce\Merchant\Onboarding_Api;
use BigCommerce\Merchant\Setup_Status;
use BigCommerce\Settings\Screens\Pending_Account_Screen;
use Pimple\Container;

/**
 * Handles merchant-related onboarding and account management for BigCommerce integration.
 *
 * Provides functionality for creating and connecting merchant accounts, checking account
 * statuses, and setting up the onboarding API.
 *
 * @package BigCommerce\Container
 */
class Merchant extends Provider {
    /**
     * The URL for the middleman used in onboarding processes.
     *
     * @var string
     */
	const MIDDLEMAN_URL   = 'merchant.middleman.url';

    /**
     * The key for accessing the Onboarding API service in the container.
     *
     * @var string
     */
	const ONBOARDING_API  = 'merchant.onboarding.api';

    /**
     * The key for accessing the Create Account service in the container.
     *
     * @var string
     */
	const CREATE_ACCOUNT  = 'merchant.onboarding.create_account';

    /**
     * The key for accessing the Connect Account service in the container.
     *
     * @var string
     */
	const CONNECT_ACCOUNT = 'merchant.onboarding.connect_account';

    /**
     * The key for accessing the Account Status service in the container.
     *
     * @var string
     */
	const ACCOUNT_STATUS  = 'merchant.onboarding.account_status';

    /**
     * The key for accessing the Setup Status service in the container.
     *
     * @var string
     */
	const SETUP_STATUS    = 'merchant.onboarding.setup_status';

    /**
     * Registers services into the dependency container.
     *
     * Sets up account onboarding services including account creation, connection,
     * status checks, and setup status.
     *
     * @param Container $container The dependency injection container.
     * @return void
     */
	public function register( Container $container ) {
		$this->account_onboarding( $container );
	}

    /**
     * Sets up account onboarding services in the container.
     *
     * Initializes and registers services for onboarding, including the middleman URL,
     * onboarding API, account creation, account connection, and account status.
     *
     * @param Container $container The dependency injection container.
     * @return void
     */
	private function account_onboarding( Container $container ) {
		$container[ self::MIDDLEMAN_URL ] = function ( Container $container ) {
			/**
			 * Filters oauth connector url
			 *
			 * @param string $url Oauth connector URL.
			 */
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

    /**
     * Registers the Create Account service and hooks into the account creation process.
     *
     * @param Container $container The dependency injection container.
     * @return void
     */
	private function create_account( Container $container ) {
		$container[ self::CREATE_ACCOUNT ] = function ( Container $container ) {
			return new Create_Account( $container[ self::ONBOARDING_API ] );
		};

		add_action( 'bigcommerce/create_account/submit_request', $this->create_callback( 'request_account', function ( $data, $errors ) use ( $container ) {
			$container[ self::CREATE_ACCOUNT ]->request_account( $data, $errors );
		} ), 10, 2 );
	}

    /**
     * Registers the Connect Account service and hooks into the account connection process.
     *
     * @param Container $container The dependency injection container.
     * @return void
     */
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

    /**
     * Registers the Account Status service and hooks into status rendering and checks.
     *
     * @param Container $container The dependency injection container.
     * @return void
     */
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

    /**
     * Registers the Setup Status service in the container.
     *
     * @param Container $container The dependency injection container.
     * @return void
     */
	private function setup_status( Container $container ) {
		$container[ self::SETUP_STATUS ] = function ( Container $container ) {
			return new Setup_Status( $container[ Api::FACTORY ] );
		};
	}
}
